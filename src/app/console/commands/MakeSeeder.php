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
 * MakeSeeder Command for generating new seeder files.
 *
 * This command is responsible for creating a new seeder file with the
 * specified name in the seeders directory. The file is created with
 * a basic template that includes a `run` method for seeding data.
 */
class MakeSeeder extends Command
{
    /**
     * Executes the seeder creation process.
     *
     * This method generates a new seeder file with the given name. It checks
     * if the file already exists and writes the seeder file with a basic
     * template that includes a `run` method for inserting data.
     */
    public function execute()
    {
        // Get the seeder name from the command arguments
        $name = $this->args[2] ?? null;

        // If no name is provided, show an error and exit
        if (!$name) {
            $this->error('Seeder name is required.');
            return;
        }

        // Define the correct path for the seeder file
        $seederPath = __DIR__ . "/../../../database/seeders/{$name}Seeder.php";

        // Check if the seeder file already exists
        if (file_exists($seederPath)) {
            $this->error("Seeder {$name} already exists.");
            return;
        }

        // Formatted name class
        $name = $name . "Seeder";

        // Basic template for the seeder file, including a placeholder run method
        $seederTemplate = <<<PHP
        <?php

        namespace Monolog\App\Database\Seeders;

        /**
         * Seeder class for seeding data for the {$name} model.
         */
        class {$name} {
            
            /**
             * Run the database seed operation.
             *
             * This method is executed when the seeder is run using mono.php command `php mono.php make:seed`.
             * It populates the database with test or default data.
             *
             * Example:
             * - Insert sample records into the {$name} table.
             * 
             * @return void
             */
            public function run() {
                // Your seeding code here
            }
        }
        PHP;

        // Create the seeder file with the provided template
        file_put_contents($seederPath, $seederTemplate);

        // Inform the user that the seeder was created successfully
        $this->info("Seeder {$name} created successfully!");
    }
}
