<?php

namespace Monolog\Database;

use PDO;
use PDOException;
use Monolog\App\Helpers\Database\DatabaseFactory;

/**
 * The Database class manages the connection to a MySQL database using PDO.
 * It ensures a single database connection instance (Singleton pattern) 
 * to optimize performance and resource usage.
 */
class Database {

    /**
     * Holds the single instance of the PDO connection.
     * 
     * @var PDO|null
     */
    private static ?PDO $connection = null;
    
    /**
     * Establishes and returns a PDO connection to the database.
     * Uses a singleton pattern to ensure that only one connection 
     * instance is created and reused.
     * 
     * @return PDO Returns the active PDO database connection.
     * @throws PDOException Throws an exception if the connection fails.
     */
    public function database(): PDO
    {
        if (!self::$connection) {
            try {
                // Create a new connection only if none exists
                self::$connection = (new DatabaseFactory)->createConnection();
            } catch (PDOException $error) {
                // Re-throw the exception with additional context
                throw new PDOException($error->getMessage(), (int) $error->getCode(), $error);
            }
        }
        return self::$connection;
    }
}
