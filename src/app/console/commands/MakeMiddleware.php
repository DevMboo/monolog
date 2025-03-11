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
 * Class MakeMiddleware
 * 
 * This class handles the creation of middleware files through a command-line interface.
 * The user can specify whether the middleware should be categorized as 'web' or 'api'.
 */
class MakeMiddleware extends Command
{
    /**
     * Executes the middleware creation process.
     *
     * This method generates a new middleware file with the given name.
     * It checks if the file already exists and, if not, creates it
     * with a basic middleware template.
     */
    public function execute()
    {
        // Retrieve command-line arguments
        $args = $this->args;

        // Ensure the required number of arguments are provided
        if (count($args) < 4) {
            $this->error("Usage: php mono.php make:middleware <MiddlewareName> -web|-api\n");
            return;
        }

        // Extract middleware name and type from arguments
        $middlewareName = ucfirst($args[2]); // Ensure the middleware name starts with an uppercase letter
        $type = $args[3]; // Middleware type (either -web or -api)

        // Determine the appropriate directory and namespace based on the type
        if ($type === '-web') {
            $path = __DIR__ . '/../../../app/middlewares/web/';
            $namespace = "Monolog\\App\\Middlewares\\Web";
        } elseif ($type === '-api') {
            $path = __DIR__ . '/../../../app/middlewares/api/';
            $namespace = "Monolog\\App\\Middlewares\\Api";
        } else {
            $this->error("Invalid option! Use -web or -api.\n");
            return;
        }

        // Ensure the directory exists, create it if necessary
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // Define the middleware file path
        $file = $path . $middlewareName . '.php';

        // Check if the middleware file already exists
        if (file_exists($file)) {
            $this->error("Middleware '{$middlewareName}' already exists!\n");
            return;
        }

        // Middleware file template
        $template = <<<PHP
                    <?php

                    namespace {$namespace};

                    use Monolog\App\Http\Request;

                    /**
                     * Middleware class responsible for handling HTTP requests.
                     * 
                     * This middleware processes incoming requests before they reach the main application logic.
                     * It can be used for authentication, logging, security checks, or modifying request data.
                     */
                    class {$middlewareName} {

                        /**
                         * Handles the middleware logic.
                         * 
                         * This method is called automatically when the middleware is executed.
                         * It receives the request and can modify it or stop further processing if needed.
                         */
                        public function handle()
                        {
                            // Middleware logic here...
                        }
                    }
                    PHP;

        // Create the middleware file with the generated template
        file_put_contents($file, $template);

        // Output success message
        $this->info("Middleware '{$middlewareName}' created successfully in {$path}\n");
    }
}
