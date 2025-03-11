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
use Monolog\Database\Database;
use Monolog\App\Helpers\Environment\Env;

use PDO;
/**
 * MakeSeed Command for executing the `run` method in all seeder files.
 *
 * This command automatically executes the `run` method in all seeder classes 
 * located in the seeders directory, without the need to manually specify 
 * each class name. It scans the seeders directory, instantiates each seeder 
 * class, and calls the `run` method to populate the database with seed data.
 */
class MakeSeed extends Command
{

    private PDO $db;

    /**
     * Constructor to initialize the database connection.
     *
     * This constructor initializes the PDO instance for database
     * interaction, ensuring that migrations can be executed and 
     * registered in the database.
     */
    public function __construct() {
        Env::load(__DIR__ . '/../../../../.env');

        // Initialize the database connection
        $this->db = (new Database)->database();
    }

    /**
     * Executes the seeding process by running the `run` method in all seeder classes.
     *
     * This method scans the seeders directory, dynamically loads all the seeder classes,
     * and invokes their `run` method to execute the seeding logic.
     * It eliminates the need to manually reference each seeder class, making the seeding 
     * process more efficient and automated.
     */
    public function execute()
    {
        // Define the path to the seeders directory
        $seederPath = __DIR__ . "/../../../database/seeders/";
        $this->info("Seeder path: {$seederPath}");

        // Check if the seeders directory exists
        if (is_dir($seederPath)) {
            // Get a list of all PHP files in the seeders directory
            $files = glob($seederPath . "*.php");

            // Iterate through each file in the seeders directory
            foreach ($files as $file) {
                $className = basename($file, ".php");
                $this->info("Processing file: {$file}, Class: {$className}");
            
                require_once $file;
            
                $fullClassName = "Monolog\\App\\Database\\Seeders\\" . $className;
            
                if (class_exists($fullClassName)) {
                    $this->info("Class {$fullClassName} found.");
                    $seeder = new $fullClassName();
            
                    if (method_exists($seeder, 'run')) {
                        $seeder->run();
                        $this->info("File {$file} is executing");
                    } else {
                        $this->error("No run method found in class {$fullClassName}");
                    }
                } else {
                    $this->error("Class {$fullClassName} not found");
                }
            }
        } else {
            // Output an error message if the seeders directory is not found
           $this->error("Seeder directory not found: {$seederPath}\n");
        }
    }
}
