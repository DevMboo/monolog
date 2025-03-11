<?php 

namespace Monolog\App\Middlewares;

use Monolog\Exceptions\Exception;

class Middlewares {

    // Stores the middleware stack configuration
    protected array $stacking;

    /**
     * Middlewares constructor.
     *
     * This constructor loads the middleware stack configuration
     * from the `stacking.php` file and executes the default middlewares.
     */
    public function __construct() {
        try {
            // Load the middleware stack configuration
            $this->stacking = require __DIR__ . '/../config/stacking.php';

            // Execute default middlewares defined in the configuration
            $this->execDefault();
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Executes the default middleware stack.
     *
     * This method loops through the default middlewares defined in the configuration
     * and calls their `handle` method if the class exists.
     *
     * @throws Exception If a middleware class is not found
     */
    public function execDefault()
    {
        try {
            foreach ($this->stacking['default'] as $middleware) {
                // Check if the middleware class exists
                if (class_exists($middleware)) {
                    // Create an instance of the middleware and call its `handle` method
                    $middlewareInstance = new $middleware();
                    $middlewareInstance->handle();
                } else {
                    // Throw an exception if the middleware class does not exist
                    throw new Exception("Middleware '{$middleware}' not found.");
                }
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Executes a specific middleware.
     *
     * This method executes a middleware specified by the parameter `$middleware`.
     * It checks if the middleware is listed in the `web` stack and if the class exists.
     *
     * @param string $middleware The name of the middleware to execute
     */
    public function execute($middleware)
    {
        try {
            // Check if the middleware exists in the 'web' stack and if the class exists
            if (isset($this->stacking['web'][$middleware]) && class_exists($this->stacking['web'][$middleware])) {
                // Create an instance of the middleware and call its `handle` method
                $middlewareClass = $this->stacking['web'][$middleware];
                $middlewareInstance = new $middlewareClass();
                $middlewareInstance->handle();
            } else {
                // Throw an exception if the middleware is not found
                throw new Exception("Middleware '{$middleware}' not found.");
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Handles exceptions by rendering an appropriate response.
     *
     * @param Exception $exception
     */
    protected function handleException(Exception $exception): void
    {
        echo $exception->render();
        exit;
    }
}
