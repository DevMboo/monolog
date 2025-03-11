<?php

namespace Monolog\Exceptions;

use Throwable;

class ValidationException extends \Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * ValidationException constructor.
     *
     * @param array $errors Array of validation error messages
     * @param string $message The error message
     * @param int $code The error code
     * @param \Throwable|null $previous The previous throwable used for the exception chaining
     */
    public function __construct(array $errors, $message = 'Validation failed', $code = 422, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Gets the validation error messages.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Render the exception as a response.
     *
     * This can be adapted for different response formats like JSON or HTML.
     *
     * @return void
     */
    public function render(): void
    {
        // Example: Return a JSON response with the error messages
        if ($_SERVER['HTTP_ACCEPT'] === 'application/json') {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => $this->getMessage(),
                'errors' => $this->getErrors(),
            ]);
        } else {
            // Example: Render a simple HTML message for the user
            echo "<h1>Error: {$this->getMessage()}</h1>";
            echo "<ul>";
            foreach ($this->getErrors() as $error) {
                echo "<li>{$error}</li>";
            }
            echo "</ul>";
        }
        exit;
    }
}
