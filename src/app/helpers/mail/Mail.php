<?php

namespace Monolog\App\Helpers\Mail;

use Monolog\App\Resources\View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * The Mail class is responsible for composing and sending emails using the PHPMailer library.
 * It allows setting recipients, subject, body content, and attachments, as well as rendering email templates.
 * The class provides methods for SMTP configuration, email body composition, and email sending.
 * 
 * This class uses PHPMailer for email sending and requires SMTP configuration to send emails.
 * It also supports dynamic email templates rendering via the View class.
 */
class Mail {

    /**
     * The PHPMailer instance used for sending emails.
     *
     * @var PHPMailer
     */
    protected PHPMailer $mailer;

    /**
     * The list of email recipients.
     *
     * @var array
     */
    protected array $recipients = [];

    /**
     * The list of email attachments.
     *
     * @var array
     */
    protected array $attachments = [];

    /**
     * The subject of the email.
     *
     * @var string|null
     */
    protected ?string $subject = null;

    /**
     * The body of the email.
     *
     * @var string|null
     */
    protected ?string $body = null;

    /**
     * The base path for email templates.
     *
     * @var string
     */
    protected string $pathTemplate = "/view/resources/views/mail/";

    /**
     * Constructor to initialize the PHPMailer instance and set up configurations.
     */
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();
    }

    /**
     * Initial setup of the PHPMailer instance.
     * Configures SMTP settings and other mailer options.
     */
    protected function setupMailer(): void {
        //$this->mailer->SMTPDebug = 2;
        //$this->mailer->Debugoutput = 'html';
        $this->mailer->isSMTP();  // Set email sending method to SMTP
        $this->mailer->Host = env('MAIL_HOST');  // Set SMTP server host
        $this->mailer->SMTPAuth = true;  // Enable SMTP authentication
        $this->mailer->Username = env('MAIL_USERNAME');  // SMTP username
        $this->mailer->Password = env('MAIL_PASSWORD');  // SMTP password
        $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION');  // Encryption method
        $this->mailer->Port = env('MAIL_PORT');  // SMTP port
        $this->mailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));  // Set sender's email and name
        $this->mailer->isHTML(true);  // Enable HTML content for the email
    }

    /**
     * Adds one or more recipients to the email.
     * 
     * @param string|array $emails The email address or an array of email addresses to send the email to.
     * @return $this The current instance of the Mail class for method chaining.
     */
    public function to(string|array $emails): self {
        foreach ((array) $emails as $email) {
            $this->recipients[] = $email;  // Add the email to the recipients list
            $this->mailer->addAddress($email);  // Add the recipient to the PHPMailer instance
        }
        return $this;
    }

    /**
     * Sets the subject of the email.
     * 
     * @param string $subject The subject of the email.
     * @return $this The current instance of the Mail class for method chaining.
     */
    public function subject(string $subject): self {
        $this->subject = $subject;
        $this->mailer->Subject = $subject;
        return $this;
    }

    /**
     * Attaches one or more files to the email.
     * 
     * @param string|array $files The file path or an array of file paths to attach.
     * @return $this The current instance of the Mail class for method chaining.
     */
    public function attach(string|array $files): self {
        foreach ((array) $files as $file) {
            if (file_exists($file)) {
                $this->attachments[] = $file;  // Add the file to the attachments list
                $this->mailer->addAttachment($file);  // Attach the file to the PHPMailer instance
            }
        }
        return $this;
    }

    /**
     * Sets the body of the email as plain text.
     * 
     * @param string $message The message to be used as the body of the email.
     * @return $this The current instance of the Mail class for method chaining.
     */
    public function body(string $message): self {
        $this->body = $message;
        $this->mailer->Body = $message;  // Set the email body
        return $this;
    }

    /**
     * Renders an email template using the View class.
     * 
     * This method first applies the layout that contains `@component(URL_PATH)`,
     * ensuring the structure is loaded before rendering the data.
     * 
     * @param string $view The name of the email template to render.
     * @param array $data The data to pass to the view.
     * @return $this The current instance of the Mail class for method chaining.
     */
    public function template(string $view, array $data = []): self {
        $viewRenderer = new View();
        
        // Render the view and apply the data
        $viewRenderer->render($view, $data);
        
        // Define the layout, where @component directives are expected to be processed
        $viewRenderer->layout($this->pathTemplate . $view);
        
        // Now, assign the processed content to the email body
        $this->body = $viewRenderer->content;  // Corrected reference to content
        
        // Assign the rendered content to the email body
        $this->mailer->Body = $this->body;

        return $this;
    }

    /**
     * Sends the email using the configured PHPMailer instance.
     * 
     * @return bool Returns true if the email was successfully sent, otherwise false.
     */
    public function send(): bool {
        try {
            echo env('MAIL_HOST');
            return $this->mailer->send();  // Attempt to send the email
        } catch (Exception $e) {
            throw new Exception("Error send mail: " . $e->getMessage(), 500);  // Log the error if sending fails
            return false;  // Return false if email sending fails
        }
    }
}
