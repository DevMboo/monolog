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
 * MakeLayout Command for generating a new layout file.
 *
 * This command is responsible for generating a new layout file in the
 * views directory. The file will be created with the specified name
 * and a basic template for the layout.
 */
class MakeLayout extends Command
{
    public function execute()
    {
        // Get the layout name from command arguments
        $layoutName = $this->args[2] ?? null;

        // If no layout name is provided, display an error and exit
        if (!$layoutName) {
            $this->error('Layout name is required.');
            return;
        }

        // Define the full path to save the layout in the resources/views directory
        $layoutPath = __DIR__ . "/../../../view/resources/views/{$layoutName}.html";

        // Check if the directory exists, if not, attempt to create it
        $directoryPath = dirname($layoutPath); // Get the directory where the layout will be saved
        if (!is_dir($directoryPath)) {
            // Try creating the directory, including subdirectories if necessary
            if (!mkdir($directoryPath, 0777, true)) {
                $this->error("Failed to create the directory: $directoryPath");
                return;
            }
        }

        // Create the layout template content
        $layoutTemplate = "<!DOCTYPE html>
                            <html lang=\"en\">
                            <head>
                                <meta charset=\"UTF-8\">
                                <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                                <title>Monolog Application</title>
                            </head>
                            <body>
                                <!-- Application render slot value -->
                                <!-- Get Starting Hacking DEV -->
                                {{slot}}
                            </body>
                            </html>";

        // Attempt to write the layout template to the file
        if (file_put_contents($layoutPath, $layoutTemplate) !== false) {
            $this->info("Layout {$layoutName} created successfully!");
        } else {
            $this->error("Failed to create layout {$layoutName}.");
        }
    }
}
