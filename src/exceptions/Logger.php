<?php

namespace Monolog\Exceptions;

use Throwable;
use DateTime;

/**
 * Class Logger
 * 
 * This class handles logging errors and exceptions to a specified log file. 
 * It provides functionality to log both general error messages and detailed exception information 
 * (including the stack trace).
 */
class Logger
{
    /**
     * Returns the log file path.
     *
     * @return string
     */
    private function getLogFilePath(): string
    {
        return __DIR__ . '/../log/monolog.log';
    }

    /**
     * Writes a log message to the log file.
     *
     * @param string $message The message to log.
     */
    private function writeToLog(string $message): void
    {
        $logFile = $this->getLogFilePath();
        file_put_contents($logFile, $message . "\n", FILE_APPEND);
    }

    /**
     * Logs errors and exceptions to the log file.
     *
     * This method will write the error message, error code, and exception details (if provided)
     * to a log file located at `log/monolog.log`. It also includes a timestamp for each log entry.
     * If the log file or directory is not writable, it will throw a RuntimeException.
     *
     * @param string $message The error or exception message to log.
     * @param int $code The error code or exception code (default is 500).
     * @param Throwable|null $exception The exception to log, including its message and stack trace (optional).
     * 
     * @throws \RuntimeException If the log file or directory is not writable.
     */
    public function logError(string $message, int $code = 500, ?Throwable $exception = null): void
    {
        // Determine the log file path
        $logFile = $this->getLogFilePath();

        // Check if the log file is writable
        if (!is_writable($logFile)) {
            // Check if the file exists
            if (!file_exists($logFile)) {
                echo "Log file does not exist: {$logFile}\n"; // Debugging
                throw new \RuntimeException("Log file does not exist and is not writable: {$logFile}");
            }
            // Check if the directory is writable
            $logDirectory = dirname($logFile);
    
            if (!is_writable($logDirectory)) {
                throw new \RuntimeException("Log directory is not writable: {$logDirectory}");
            }
        }

        // Create a DateTime object to timestamp the log entry
        $dateTime = new DateTime();
        $timestamp = $dateTime->format('Y-m-d H:i:s');

        // Prepare the log message
        $logMessage = "[{$timestamp}] Error Code: {$code} - {$message}";

        // If an exception is provided, include the stack trace
        if ($exception) {
            $logMessage .= "\nException Details: " . $exception->getMessage() . "\n";
            $logMessage .= "Stack Trace: " . $exception->getTraceAsString() . "\n";
        }
        
        // Write the log message to the log file
        $this->writeToLog($logMessage);
    }

    /**
     * Logs an exception that is thrown during the application execution.
     *
     * This method calls `logError` with the exception's message, code, and the exception itself
     * to log the details of the exception.
     *
     * @param Throwable $exception The exception to log.
     */
    public function logException(Throwable $exception): void
    {
        // Log the exception message and code
        $this->logError($exception->getMessage(), $exception->getCode(), $exception);
    }

    /**
     * Logs an informational message to the log file.
     *
     * This method allows you to insert simple informational log entries (such as debug messages) 
     * without providing an error code or exception. It is intended for use in production or development 
     * environments to log useful messages.
     *
     * @param string $message The informational message to log.
     */
    public function logInfo(string $message): void
    {
        // Create a DateTime object to timestamp the log entry
        $dateTime = new DateTime();
        $timestamp = $dateTime->format('Y-m-d H:i:s');

        // Prepare the informational log message
        $logMessage = "[{$timestamp}] INFO: {$message}";

        // Write the log message to the log file
        $this->writeToLog($logMessage);
    }
}
