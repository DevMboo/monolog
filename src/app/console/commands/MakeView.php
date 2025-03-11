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
 * MakeView Command for generating a new view file.
 *
 * This command is responsible for generating a new view file in the
 * views directory. The file will be created with the specified name
 * and a basic template for the view.
 */
class MakeView extends Command
{
    /**
     * Executes the view creation process.
     *
     * This method generates a new view file with the given name.
     * It checks if the file already exists and, if not, creates the
     * file with a basic template for a view.
     */
    public function execute()
    {
        // Get the name of the view from the command arguments
        $name = $this->args[2] ?? null;

        // If no name is provided, output an error
        if (!$name) {
            $this->error('View name is required.');
            return;
        }

        // Define the path for the new view file
        $viewPath = __DIR__ . "/../../../view/resources/views/pages/{$name}.html";

        // Check if the view file already exists
        if (file_exists($viewPath)) {
            $this->error("View {$name}.html already exists.");
            return;
        }

        // Define the basic template for the view
        $viewTemplate = "<!-- {$name} view file -->\n\n<h1>Welcome to the {$name} view!</h1>";

        // Create the new view file with the template
        file_put_contents($viewPath, $viewTemplate);

        // Output success message
        $this->info("View {$name}.html created successfully!");
    }
}
