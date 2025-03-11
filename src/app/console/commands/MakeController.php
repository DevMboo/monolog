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
 * MakeController Command for generating a new controller.
 *
 * This class handles the creation of a new controller file in the 
 * Monolog framework. It checks if the controller already exists 
 * and generates a basic controller template if it doesn't.
 */
class MakeController extends Command {

    /**
     * Executes the command to create a new controller.
     *
     * This method checks if a controller name is provided and if the 
     * controller already exists. If the controller doesn't exist, 
     * it generates a new controller file with a default template.
     */
    public function execute() {
        // Retrieve the controller name from the command arguments.
        $name = $this->args[2] ?? null;

        // If no name is provided, display an error message.
        if (!$name) {
            $this->error('Controller name is required.');
            return;
        }

        // Define the path where the controller will be created.
        $controllerPath = __DIR__ . "/../../controller/{$name}Controller.php";

        // Check if the controller already exists at the specified path.
        if (file_exists($controllerPath)) {
            $this->error("Controller {$name} already exists.");
            return;
        }

        // Define the properly formatted controller template.
        $controllerTemplate = <<<PHP
                            <?php

                            namespace Monolog\App\Controller;

                            use Monolog\App\Http\Request;
                            use Monolog\App\Controller\Controller;

                            /**
                             * -------------------------------------------------------------
                             * Base Controller Class
                             * -------------------------------------------------------------
                             * 
                             * This class provides a structured way to handle HTTP requests
                             * within the application's MVC architecture.
                             * 
                             * Controllers process requests, interact with models, and return
                             * responses, such as views or JSON data.
                             * 
                             * Each controller extends the base `Controller` class, which
                             * offers shared functionality like middleware support and
                             * response handling.
                             * 
                             */
                            class {$name}Controller extends Controller
                            {
                                /**
                                 * Default method for handling requests.
                                 *
                                 * Typically used to list resources or render a view.
                                 *
                                 * @return mixed
                                 */
                                public function index()
                                {
                                    // 
                                }
                            }
                            PHP;

        // Create the new controller file with the template.
        file_put_contents($controllerPath, $controllerTemplate);

        // Inform the user that the controller has been successfully created.
        $this->info("Controller {$name}Controller created successfully!");
    }
}
