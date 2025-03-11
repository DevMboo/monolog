<?php

namespace Monolog\App\Helpers\Database;

use PDO;
use PDOException;

/**
 * The DatabaseFactory class is responsible for creating database connections.
 * It supports multiple database drivers and retrieves connection settings 
 * from environment variables.
 */
class DatabaseFactory {
    
    /**
     * Database host (server address).
     *
     * @var string
     */
    private string $hostname;

    /**
     * Database name.
     *
     * @var string
     */
    private string $database;

    /**
     * Database username.
     *
     * @var string
     */
    private string $username;

    /**
     * Database password.
     *
     * @var string
     */
    private string $password;

    /**
     * Database port.
     *
     * @var string
     */
    private string $port;

    /**
     * Database driver type (e.g., MySQL, PostgreSQL, SQLite, SQL Server).
     *
     * @var string
     */
    private string $driver;

    /**
     * Initializes database connection settings from environment variables.
     */
    public function __construct() {
        $this->hostname = env("DB_HOST");
        $this->database = env("DB_NAME");
        $this->username = env("DB_USER");
        $this->password = env("DB_PASSWORD");
        $this->port = env("DB_PORT");
        $this->driver = env("DB_DRIVER", "mysql");
    }

    /**
     * Creates and returns a PDO connection based on the selected database driver.
     *
     * @return PDO Returns an instance of the PDO connection.
     * @throws PDOException Throws an exception if the driver is unsupported.
     */
    public function createConnection(): PDO {
        switch (strtolower($this->driver)) {
            case "mysql":
                return $this->connectMySQL();
            case "pgsql":
                return $this->connectPostgreSQL();
            case "sqlsrv":
                return $this->connectSQLServer();
            case "sqlite":
                return $this->connectSQLite();
            default:
                throw new PDOException("Unsupported database driver: {$this->driver}");
        }
    }

    /**
     * Establishes a connection to a MySQL database.
     *
     * @return PDO Returns a PDO connection instance.
     */
    private function connectMySQL(): PDO {
        return $this->connect(
            "mysql:host={$this->hostname};port={$this->port};dbname={$this->database}",
            $this->username,
            $this->password
        );
    }

    /**
     * Establishes a connection to a PostgreSQL database.
     *
     * @return PDO Returns a PDO connection instance.
     */
    private function connectPostgreSQL(): PDO {
        return $this->connect(
            "pgsql:host={$this->hostname};port={$this->port};dbname={$this->database}",
            $this->username,
            $this->password
        );
    }

    /**
     * Establishes a connection to a SQL Server (SQLSRV) database.
     *
     * @return PDO Returns a PDO connection instance.
     */
    private function connectSQLServer(): PDO {
        return $this->connect(
            "sqlsrv:Server={$this->hostname},{$this->port};Database={$this->database}",
            $this->username,
            $this->password
        );
    }

    /**
     * Establishes a connection to an SQLite database.
     *
     * @return PDO Returns a PDO connection instance.
     */
    private function connectSQLite(): PDO {
        return $this->connect("sqlite:{$this->database}");
    }

    /**
     * Creates a new PDO connection.
     *
     * @param string $dsn The Data Source Name (DSN) for the connection.
     * @param string $username (Optional) The database username.
     * @param string $password (Optional) The database password.
     * @return PDO Returns a new PDO instance.
     * @throws PDOException Throws an exception if the connection fails.
     */
    private function connect(string $dsn, string $username = "", string $password = ""): PDO {
        try {
            return new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $error) {
            throw new PDOException("Database connection failed: " . $error->getMessage());
        }
    }
}
