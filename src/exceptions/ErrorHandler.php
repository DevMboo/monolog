<?php

namespace Monolog\Exceptions;

use Throwable;

/**
 * Class ErrorHandler
 *
 * This class is responsible for handling exceptions thrown in the application.
 * It checks if the exception is an instance of \Error and creates a custom
 * exception to render a detailed error page. If the exception is not an \Error,
 * a generic error message is displayed instead.
 *
 * The `handle()` method takes a Throwable instance as an argument and processes
 * it accordingly, either rendering a custom error page for \Error instances or
 * displaying a basic error message for other types of exceptions.
 */
class ErrorHandler
{
    /**
     * Handle the given exception.
     *
     * This method processes the exception by checking if it's an instance of \Error.
     * If it is, it creates a custom Exception and displays a rendered error page.
     * Otherwise, it displays a generic error message.
     *
     * @param \Throwable $exception The thrown exception.
     * @return void
     */
    public function handle(\Throwable $exception): void
    {
        // Check if the exception is of type \Error (e.g., class not found)
        if ($exception instanceof \Error) {
            // Create a custom exception using the message, code (500), and previous exception
            $customException = new Exception(
                $exception->getMessage(), // Error message from the original exception
                500, // HTTP status code for internal server error
                $exception // The original exception is passed as the previous exception
            );

            // Render and output the custom error page
            echo $customException->render();
        } else {
            // If the exception is not of type \Error, display a generic error message
            echo "<h1>An error occurred</h1>";
            echo "<p>{$exception->getMessage()}</p>";
        }
    }
}
