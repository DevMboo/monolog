<?php

namespace Monolog\App\Http;

use Monolog\Exceptions\Exception;
use Monolog\Exceptions\Logger;

/**
 * Class Response
 *
 * Manages HTTP responses, including status codes, redirections, and error rendering.
 */
class Response {
    
    /**
     * HTTP status code.
     *
     * @var int
     */
    protected int $statusCode = 200;

    /**
     * Sets the HTTP status code.
     *
     * @param int $code
     * @return $this
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        http_response_code($code);
        return $this;
    }

    /**
     * Gets the current status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Redirects to a URL.
     *
     * @param string $url
     * @param int $statusCode
     */
    public function redirect(string $url, int $statusCode = 302): void
    {
        $this->setStatusCode($statusCode);
        header("Location: $url");
        exit;
    }

    /**
     * Redirects the user back to the previous page.
     */
    public function back(): void
    {
        // Verifica se o cabeçalho "HTTP_REFERER" está presente
        $previousUrl = $_SERVER['HTTP_REFERER'] ?? '/'; // Redireciona para a página inicial caso não exista o referer
        $this->redirect($previousUrl);
    }

    /**
     * Handles an exception and renders the error response.
     *
     * @param \Throwable $exception
     */
    public function handleException(\Throwable $exception): void
    {
        $this->setStatusCode($exception->getCode() ?: 500);
    
        if ($this->isJsonRequest()) {
            $this->json([
                'error' => true,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        } else {
            if ($exception instanceof \Monolog\Exceptions\Exception) {
                echo $exception->render();
            } else {
                $errorHandler = new \Monolog\Exceptions\ErrorHandler();
                $errorHandler->handle($exception);
            }
        }

        (new Logger)->logException($exception);
    
        exit;
    }

    /**
     * Returns a JSON response.
     *
     * @param mixed $data
     */
    public function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Checks if the request expects a JSON response.
     *
     * @return bool
     */
    public function isJsonRequest(): bool
    {
        return strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
    }

    /**
     * Sends the response based on the request type.
     *
     * @param mixed $data
     */
    public function send($data): void
    {
        if ($this->isJsonRequest()) {
            $this->json($data);
        } else {
            echo $data;
        }
    }

    /**
     * Sets a flash message in the session.
     *
     * @param string $name
     * @param string $message
     */
    public function setMessage(string $name, string $message): self
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['messages'][$name] = $message;

        return $this;
    }

    /**
     * Gets a flash message from the session.
     *
     * @param string $name
     * @return string|null
     */
    public function getMessage(string $name): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['messages'][$name])) {
            return null;
        }

        $message = $_SESSION['messages'][$name];
        unset($_SESSION['messages'][$name]); // Apaga após ler
        return $message;
    }

    /**
     * Checks if a flash message exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasMessage(string $name): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['messages'][$name]);
    }
}
