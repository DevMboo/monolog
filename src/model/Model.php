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
namespace Monolog\Model;

use Monolog\Database\Database;

use PDO;
use Monolog\Exceptions\Exception;

/**
 * Abstract Model class
 *
 * This is an abstract class that provides common database operations for interacting
 * with a specific database table. It extends the `Database` class and assumes that
 * each subclass will define a table name (`$tbname`).
 *
 * This class provides methods for basic CRUD operations (Create, Read, Update, Delete),
 * as well as support for dynamic querying with the `findBy` pattern.
 *
 * It is intended to be extended by other models that require database interaction.
 */
abstract class Model extends Database {

    // Database connection instance
    protected PDO $db;
    
    // Table name
    protected string $tbname;

    /**
     * Constructor initializes the database connection.
     * Throws an exception if the table name is not set.
     */
    public function __construct() {
        try {
            // Initialize the database connection
            $this->db = (new Database)->database();
    
            // Check if the table name is set, otherwise throw an exception
            if (!isset($this->tbname)) {
                throw new Exception("Table name not defined.", 500);
            }
        } catch (Exception $e) {
            // Render window error
            echo $e->render();
            exit;
        }
    }

    /**
     * Retrieves all rows from the table.
     *
     * @return array Returns an array of all rows from the table
     */
    public function all(): array
    {
        // Build the query to select all records from the table
        $query = "SELECT * FROM {$this->tbname}";

        // Execute the query and return the result
        return $this->builder($query);
    }

    /**
     * Finds a record by its ID.
     *
     * @param int $id The ID of the record
     * @return array|null Returns the record as an associative array or null if not found
     */
    public function find(int $id): ?array
    {
        // Build the query to find a record by ID
        $query = "SELECT * FROM {$this->tbname} WHERE id = :id LIMIT 1";
        $result = $this->builder($query, ['id' => $id]);

        // Return the result (first record or null)
        return $result ? $result[0] : null;
    }

    /**
     * Creates a new record in the table.
     *
     * @param array $data The data to insert
     * @return bool Returns true if the record was created successfully, false otherwise
     */
    public function create(array $data): bool
    {
        // Build the columns and placeholders for the insert statement
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        // Build the query to insert a new record
        $query = "INSERT INTO {$this->tbname} ({$columns}) VALUES ({$placeholders})";

        // Execute the query
        return $this->execute($query, $data);
    }

    /**
     * Updates an existing record by ID.
     *
     * @param int $id The ID of the record to update
     * @param array $data The data to update
     * @return bool Returns true if the record was updated successfully, false otherwise
     */
    public function update(int $id, array $data): bool
    {
        // Build the fields for the update statement
        $fields = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));

        // Build the query to update the record
        $query = "UPDATE {$this->tbname} SET {$fields} WHERE id = :id";
        $data['id'] = $id;

        // Execute the query
        return $this->execute($query, $data);
    }

    /**
     * Deletes a record by ID.
     *
     * @param int $id The ID of the record to delete
     * @return bool Returns true if the record was deleted successfully, false otherwise
     */
    public function delete(int $id): bool
    {
        // Build the query to delete the record
        $query = "DELETE FROM {$this->tbname} WHERE id = :id";

        // Execute the query
        return $this->execute($query, ['id' => $id]);
    }

    /**
     * Executes a prepared statement.
     *
     * @param string $query The SQL query to execute
     * @param array $params The parameters to bind to the query
     * @return bool Returns true if the query was executed successfully, false otherwise
     */
    private function execute(string $query, array $params = []): bool
    {
        // Prepare the statement
        $stmt = $this->db->prepare($query);

        // Execute the statement with the provided parameters
        return $stmt->execute($params);
    }

    /**
     * Magic method to handle dynamic findBy methods.
     *
     * @param string $method The method name (e.g., findByName)
     * @param array $arguments The arguments passed to the method
     * @return array|null Returns the found record or null if not found
     * @throws Exception Throws an exception if the method does not exist
     */
    public function __call($method, $arguments)
    {
        try {
            // Check if the method starts with "findBy"
            if (strpos($method, 'findBy') === 0) {
                // Extract the column name from the method name
                $column = strtolower(str_replace('findBy', '', $method));
    
                // Build the query to find a record by the specified column
                $query = "SELECT * FROM {$this->tbname} WHERE {$column} = :value LIMIT 1";
                $result = $this->builder($query, ['value' => $arguments[0]]);
    
                // Return the result (first record or null)
                return $result ? $result[0] : null;
            }
    
            // Throw an exception if the method does not exist
            throw new Exception("Method {$method} does not exist in the model.", 400);
        } catch (Exception $e) {
            // Render error window
            echo $e->render();
            exit;
        }
    }

    /**
     * Executes a query and returns the results.
     *
     * @param string $query The SQL query to execute
     * @param array $params The parameters to bind to the query
     * @return array Returns the query result as an associative array
     */
    public function builder(string $query, array $params = []): array
    {
        // Prepare the statement
        $stmt = $this->db->prepare($query);

        // Execute the statement with the provided parameters
        $stmt->execute($params);

        // Return the result as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
