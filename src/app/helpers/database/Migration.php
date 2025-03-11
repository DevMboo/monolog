<?php

namespace Monolog\App\Helpers\Database;

use Monolog\Database\Database;
use PDO;

/**
 * Abstract class Migration
 * 
 * This class provides a base for database migrations, allowing for table creation, modification, and rollback operations.
 */
abstract class Migration {
    
    /**
     * @var PDO $db The database connection instance.
     */
    protected PDO $db;

    /**
     * Migration constructor.
     * 
     * Initializes the database connection using the Database class.
     */
    public function __construct() {
        $this->db = (new Database())->database(); // Assumes the Database class is already configured
    }

    /**
     * Method responsible for creating or modifying database tables.
     * 
     * This method should be implemented in derived migration classes.
     */
    abstract public function up();

    /**
     * Method responsible for rolling back operations performed in the `up` method.
     * 
     * This method should be implemented in derived migration classes.
     */
    abstract public function down();
    
    /**
     * Helper method to execute SQL queries with optional parameters.
     * 
     * @param string $query The SQL query to execute.
     * @param array $params Optional parameters for prepared statements.
     * @return bool Returns true on success, false on failure.
     */
    protected function executeQuery(string $query, array $params = []) {
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }
    
    /**
     * Method to create a database table.
     * 
     * @param string $tableName The name of the table to be created.
     * @param array $columns An associative array where keys are column names and values are their data types.
     * @return bool Returns true on success, false on failure.
     */
    protected function createTable(string $tableName, array $columns) {
        // Initialize an empty array to store column definitions
        $columnsSql = [];
        
        // Loop through the columns array to ensure column names and types are formatted correctly
        foreach ($columns as $column => $type) {
            // Add each column definition to the $columnsSql array, with backticks around column names
            // This ensures column names are correctly formatted, especially for reserved words or special characters
            $columnsSql[] = "`{$column}` {$type}";
        }
        
        // Join all the column definitions into a single string, separated by commas
        // This creates the final part of the SQL query with all the columns defined
        $columnsSql = implode(", ", $columnsSql);
        
        // Create the final SQL query string for creating the table
        // The table name and the formatted column definitions are inserted into the query
        $query = "CREATE TABLE IF NOT EXISTS `{$tableName}` ({$columnsSql});";
        
        // Execute the query using the executeQuery method
        // This runs the SQL query to create the table in the database
        return $this->executeQuery($query);
    }
    
    
    

    /**
     * Method to drop a database table.
     * 
     * @param string $tableName The name of the table to be dropped.
     * @return bool Returns true on success, false on failure.
     */
    protected function dropTable(string $tableName) {
        $query = "DROP TABLE IF EXISTS {$tableName};";
        return $this->executeQuery($query);
    }
}
