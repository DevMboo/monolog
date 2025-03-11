<?php

/**
 * Monolog PHP Framework.
 * Version 1.0 (2025).
 *
 * Monolog is a fresh take on project structure, inspired by Laravel and CodeIgniter 4.
 * It aims to provide a clean, efficient, and developer-friendly architecture.
 *
 * @see       https://github.com/devMboo/monolog The Monolog GitHub repository
 *
 * @author    Luan Chaves <lchavesdesousa>
 * @copyright 2025 Luan Chaves
 * @license   https://opensource.org/licenses/MIT MIT License
 * @note      This framework is distributed in the hope that it will be useful, 
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of 
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
namespace Monolog\App\Console\Commands;

use Monolog\App\Console\Command;

/**
 * MakeMail Command for generating a new mail template and recipient component.
 *
 * This command generates both the main email template and the recipient 
 * component, ensuring a structured way to build email templates.
 */
class MakeMail extends Command
{
    public function execute()
    {
        // Get the mail name from command arguments
        $mailName = $this->args[2] ?? null;

        // If no mail name is provided, display an error and exit
        if (!$mailName) {
            $this->error('Mail name is required.');
            return;
        }

        // Define paths for email template and recipient component
        $mailPath = __DIR__ . "/../../../view/resources/views/mail/{$mailName}.html";
        $recipientPath = __DIR__ . "/../../../view/resources/views/mail/recipient/{$mailName}Recipient.html";

        // Ensure directories exist
        $this->ensureDirectory(dirname($mailPath));
        $this->ensureDirectory(dirname($recipientPath));

        /**
         * Email template structure.
         * 
         * The template includes the @recipient directive, which dynamically 
         * loads the recipient component content from the specified path.
         * 
         * {{slot}} will be replaced with the main email content.
         */
        $mailTemplate = "<!DOCTYPE html>
                            <html lang=\"en\">
                            <head>
                                <meta charset=\"UTF-8\">
                                <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                                <title>{$mailName} - Monolog Mail</title>
                            </head>
                            <body>
                                <!-- Email Content -->
                                <!-- 
                                    * Renders the specified component with the given data.
                                    * 
                                    * This method loads the corresponding component template file, 
                                    * processes embedded directives (such as `@recipient(URL_OF_COMPONENT)`), 
                                    * and replaces placeholders with the provided data.
                                    * Use @recipient when sending emails, structuring 
                                    * the file according to your needs.
                                -->
                                {{slot}}
                            </body>
                            </html>";

        /**
         * Recipient component structure.
         * 
         * This section defines the body of the email content.
         * The {{slot}} placeholder will be replaced dynamically with the email content.
         */
        $recipientTemplate = "<div>
                                <!-- Email Recipient Content -->
                                {{slot}}
                            </div>";

        // Write files
        $this->createFile($mailPath, $mailTemplate, "Mail template {$mailName}.html");
        $this->createFile($recipientPath, $recipientTemplate, "Recipient component {$mailName}Recipient.html");
    }

    /**
     * Ensures a directory exists, creating it if necessary.
     *
     * @param string $directory The directory path to check or create.
     */
    private function ensureDirectory(string $directory)
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true)) {
                $this->error("Failed to create directory: $directory");
                exit;
            }
        }
    }

    /**
     * Creates a file with the given content and displays a success or error message.
     *
     * @param string $path The file path to create.
     * @param string $content The content to write to the file.
     * @param string $description A description of the created file for logging.
     */
    private function createFile(string $path, string $content, string $description)
    {
        if (file_put_contents($path, $content) !== false) {
            $this->info("Successfully created: $description");
        } else {
            $this->error("Failed to create: $description");
        }
    }
}
