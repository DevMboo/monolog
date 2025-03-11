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
 * MakeModel Command for generating new model files.
 *
 * This command is responsible for creating a new model file with the 
 * specified name in the models directory. The file is created with 
 * a basic template that includes the model's namespace and a 
 * `dbname` property initialized with the lowercase model name.
 */
class MakeModel extends Command
{
    /**
     * Executes the model creation process.
     *
     * This method generates a new model file with the given name. It checks
     * if the file already exists, creates the necessary directories, and 
     * writes the model file with a basic template.
     */
    public function execute()
    {
        // Get the model name from the command arguments
        $name = $this->args[2] ?? null;

        // If no name is provided, show an error and exit
        if (!$name) {
            $this->error('Model name is required.');
            return;
        }

        // Define the correct path for the model file
        $modelPath = __DIR__ . "/../../../model/{$name}.php";

        // Check if the model file already exists
        if (file_exists($modelPath)) {
            $this->error("Model {$name} already exists.");
            return;
        }

        // Define the model name in lowercase for the dbname property
        $namelowercase = strtolower($name);

        // Basic template for the model file
        $modelTemplate = <<<PHP
                        <?php

                        namespace Monolog\Model;

                        use Monolog\Model\Model;

                        /**
                         * Model class for interacting with the {$name} table in the database.
                         * 
                         * This model represents the structure and behavior of the {$name} entity,
                         * allowing CRUD operations and interactions with the related database table.
                         *
                         * Example:
                         * - Fetching data related to {$name}
                         * - Inserting, updating, or deleting {$name} records in the database.
                         *
                         * @package Monolog\Model
                         */
                        class {$name} extends Model {

                            /**
                             * The name of the database table associated with this model.
                             *
                             * @var string
                             */
                            protected string \$tbname = "{$namelowercase}";

                        }
                        PHP;

        // Check if the target directory exists, and create it if necessary
        $directory = dirname($modelPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true); // Create the directory recursively
        }

        // Create the model file with the provided template
        file_put_contents($modelPath, $modelTemplate);

        // Inform the user that the model was created successfully
        $this->info("Model {$name} created successfully!");
    }
}
