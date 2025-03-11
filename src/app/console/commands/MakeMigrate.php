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
 * MakeMigrate Command for executing database migrations.
 *
 * This command is responsible for checking and applying all pending 
 * migrations by comparing them with the migrations that have been 
 * executed, and applying any new migrations that have not been 
 * previously executed.
 */
class MakeMigrate extends Command
{

    /**
     * The string path files migrations.
     *
     * @var string
     */
    private string $migrationsPath = __DIR__ . '/../../../database/migrations';

    /**
     * The database instance.
     *
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor to initialize the database connection.
     *
     * This constructor initializes the PDO instance for database
     * interaction, ensuring that migrations can be executed and 
     * registered in the database.
     */
    public function __construct($args)
    {
        // Load environment variebles
        Env::load(__DIR__ . '/../../../../.env');

        // Initialize the database connection
        $this->db = (new Database)->database();

        // Load args values
        parent::__construct($args);
    }

    /**
     * Executes the migration process.
     *
     * This method checks the migrations directory for new migration 
     * files that have not been applied to the database. If any new 
     * migrations are found, it applies them by calling their 'up' method 
     * and registers the migration in the database.
     */
    public function execute()
    {
        // Ensure the migrations table exists
        $this->checkMigrationsTable();

        // Check if the rollback flag (-rollback) is present in the arguments
        if (in_array('-rollback', $this->args)) {
            $this->revertMigrations();
            exit;
        }

        // Retrieve a list of migrations that have already been executed
        $executedMigrations = $this->getExecutedMigrations();

        // Get a list of all migration files in the migrations directory
        $migrationFiles = glob($this->migrationsPath . '/*.php');

        // Execute pending migrations
        $this->generateMigrations($migrationFiles, $executedMigrations);
    }

    /**
     * Runs pending migrations that have not been executed yet.
     *
     * @param array $migrationFiles List of migration files in the directory.
     * @param mixed $executedMigrations List of already executed migrations.
     */
    private function generateMigrations(array $migrationFiles, mixed $executedMigrations)
    {
        // Loop through each migration file
        foreach ($migrationFiles as $file) {
            // Extract the migration file name (without path)
            $migrationName = basename($file);

            // If the migration has not been executed yet, run it
            if (!in_array($migrationName, $executedMigrations)) {
                $this->info("Executing migration: $migrationName\n");

                // Include the migration file and get the anonymous class instance
                $migration = require $file;

                // Check if the migration has an 'up' method and run it
                if (method_exists($migration, 'up')) {
                    $migration->up();
                    $this->registerMigration($migrationName);
                    $this->info("Migration $migrationName applied successfully.\n");
                } else {
                    $this->error("Error: Migration $migrationName does not have an 'up' method.\n");
                }
            } else {
                // Skip migrations that have already been executed
                echo "Migration $migrationName has already been executed. Skipping...\n";
            }
        }
    }

    /**
     * Rolls back the last executed migrations.
     *
     * The number of migrations to roll back can be specified in the command arguments.
     * By default, only the last migration is reverted.
     */
    private function revertMigrations()
    {
        // Get the number of migrations to roll back (default is 1)
        $numberRows = isset($this->args[3]) ? (int) $this->args[3] : 1;

        // Retrieve the last applied migrations from the database
        $query = "SELECT migration_name FROM migrations ORDER BY id DESC LIMIT $numberRows";
        $stmt = $this->db->query($query);
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN); // Retrieve only the migration names

        // If there are no migrations to rollback, display a message and exit
        if (empty($migrations)) {
            $this->info("No migrations found for rollback.\n");
            return;
        }

        // Rollback each retrieved migration
        foreach ($migrations as $migrationName) {
            $filePath = $this->migrationsPath . '/' . $migrationName;

            // Check if the migration file exists
            if (!file_exists($filePath)) {
                $this->error("Error: Migration file $migrationName not found.\n");
                continue;
            }

            // Include the migration file and get the instance
            $migration = require $filePath;

            // Check if the migration has a 'down' method and execute it
            if (method_exists($migration, 'down')) {
                $migration->down();
                $this->removeMigration($migrationName); // Remove migration record from the database
                $this->info("Rollback completed for $migrationName.\n");
            } else {
                $this->error("Error: Migration $migrationName does not have a 'down' method.\n");
            }
        }

        $this->info("Rollback process completed.\n");
    }

    /**
     * Removes a migration entry from the migrations table after rollback.
     *
     * @param string $migrationName The name of the migration to be removed.
     */
    private function removeMigration(string $migrationName)
    {
        $query = "DELETE FROM migrations WHERE migration_name = :migration_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['migration_name' => $migrationName]);
    }

    /**
     * Ensures that the `migrations` table exists in the database.
     *
     * This method checks if the migrations table exists and creates it 
     * if it doesn't. The table is used to track the migrations that have 
     * been executed.
     */
    private function checkMigrationsTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration_name VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($query);
    }

    /**
     * Retrieves a list of migrations that have already been executed.
     *
     * This method queries the `migrations` table to get a list of 
     * migration names that have already been applied.
     *
     * @return array The list of executed migrations.
     */
    private function getExecutedMigrations(): array
    {
        $query = "SELECT migration_name FROM migrations";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Registers a migration as executed in the database.
     *
     * This method inserts a record of the executed migration into the 
     * `migrations` table to ensure that the migration is not re-applied 
     * in future runs.
     *
     * @param string $migrationName The name of the migration to register.
     */
    private function registerMigration(string $migrationName)
    {
        $stmt = $this->db->prepare("INSERT INTO migrations (migration_name) VALUES (:migration_name)");
        $stmt->execute(['migration_name' => $migrationName]);
    }
}
