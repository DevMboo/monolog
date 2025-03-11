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

use DateTime;
use Monolog\App\Console\Command;

/**
 * MakeMigration Command for generating new migration files.
 *
 * This command is responsible for creating a new migration file with 
 * a timestamped name in the migrations directory. The file is created 
 * using a basic template for the migration class.
 */
class MakeMigration extends Command {

    /**
     * Executes the migration creation process.
     *
     * This method generates a new migration file with a timestamped name 
     * and a basic template. It also checks if the migration already exists 
     * before creating a new one.
     */
    public function execute() {
        // Get the migration name from the command arguments
        $name = $this->args[2] ?? null;

        // If no name is provided, show an error and exit
        if (!$name) {
            $this->error('Migration name is required.');
            return;
        }

        // Get the current timestamp in the format YYYY_MM_DD_HH_MM_SS
        $realtime = new DateTime();
        $timestamp = $realtime->format('Y_m_d_H_i_s');

        // Format the migration name (using the timestamp and the name provided)
        $migrationName = "{$timestamp}_create_" . strtolower($name) . "_table";

        // Set the migration file path
        $migrationPath = __DIR__ . "/../../../database/migrations/{$migrationName}.php";

        // Check if the migration file already exists
        if (file_exists($migrationPath)) {
            $this->error("Migration {$migrationName} already exists.");
            return;
        }

        // Basic template for the migration file, including a placeholder for up/down methods
        $migrationTemplate = <<<PHP
                            <?php
                            namespace Monolog\App\Database\Migrations;

                            use Monolog\App\Helpers\Database\Migration;

                            return new class extends Migration {
                                public function up() {
                                    /**
                                     * Runs the migration to create the $name table.
                                     */
                                }

                                public function down() {
                                    /**
                                     * Rolls back the migration by dropping the $name table.
                                     */
                                }
                            };
                            PHP;

        // Create the migration file with the generated content
        file_put_contents($migrationPath, $migrationTemplate);

        // Inform the user that the migration was created successfully
        $this->info("Migration {$migrationName} created successfully!");
    }
}
