<?php

namespace Monolog\Exceptions;

use Throwable;
use Monolog\Exceptions\Logger;

/**
 * Class Exception
 *
 * Handles application exceptions, providing detailed error messages
 * this is class render in application error screen.
 */
class Exception extends \Exception
{
    /**
     * Exception constructor.
     *
     * @param string $message The error message.
     * @param int $code The error code.
     * @param Throwable|null $previous The previous exception if any.
     */
    public function __construct(string $message, int $code = 500, ?Throwable $previous = null)
    {
        (new Logger())->logException($this);
        parent::__construct($message, $code, $previous);
    }

    /**
     * Generates a detailed HTML error page.
     *
     * @return string
     */
    public function render(): string
    {
        return <<<HTML
        <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <link rel='shortcut icon' href='/src/view/public/ico/ico.png' type='image/x-icon'>
                <title>Internal Error {$this->getCode()}</title>
                <style>
                    body { font-family: Arial, sans-serif; background: #f8f8f8; color: #333; text-align: center; padding: 50px; }
                    .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); display: inline-block; max-width: 800px; }
                    h1 { color: #d9534f; }
                    .details { text-align: left; margin-top: 20px; }
                    .details pre { background: #eee; padding: 10px; border-radius: 5px; overflow-x: auto; }
                    .monoico { width: 90px; height: 90px}
                </style>
            </head>
            <body>
                <div class='container'>
                    <img src='/src/view/public/ico/ico.png' class='monoico' />
                    <h1>Error {$this->getCode()}</h1>
                    <p><strong>{$this->getMessage()}</strong></p>
                    <div class='details'>
                        <h3>File:</h3>
                        <pre>{$this->getFile()} : {$this->getLine()}</pre>
                        <h3>Stack Trace:</h3>
                        <pre>{$this->formatTrace()}</pre>
                    </div>
                </div>
            </body>
        </html>
        HTML;
    }

    /**
     * Formats the stack trace for better readability.
     *
     * @return string
     */
    private function formatTrace(): string
    {
        return implode("\n", array_map(function ($trace) {
            return isset($trace['file']) 
                ? "{$trace['file']} : {$trace['line']}"
                : "[internal function]";
        }, $this->getTrace()));
    }
}
