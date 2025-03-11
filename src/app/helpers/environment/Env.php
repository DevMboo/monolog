<?php

namespace Monolog\App\Helpers\Environment;

use Monolog\Exceptions\Exception;
use Monolog\Exceptions\Logger;

/**
 * Class Env
 * 
 * Responsible for loading and managing environment variables from a `.env` file.
 */
class Env
{
    /**
     * Stores the loaded environment variables.
     *
     * @var array
     */
    private static array $variables = [];

    /**
     * Loads environment variables from the `.env` file and stores them in memory.
     *
     * @param string $path Path to the `.env` file.
     * @throws Exception If the `.env` file is not found.
     */
    public static function load(string $path)
    {

        try {
            if (!file_exists($path)) {
                throw new Exception("File .env not found: $path", 404);
            }
    
            // Read all lines from the file, ignoring empty lines
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                // Ignore comments in the file
                if (str_starts_with(trim($line), '#')) {
                    continue;
                }
    
                // Split the line at the first occurrence of "="
                [$key, $value] = explode('=', $line, 2) + [null, null];
    
                // If the line does not contain a valid key and value, skip it
                if (!isset($key) || !isset($value)) {
                    continue;
                }
    
                // Remove quotes if present in the value
                $value = trim($value, "\"'");
    
                // Store the variable in the array
                self::$variables[$key] = $value;
            }
        } catch (Exception $e) {
            echo $e->render();
            (new Logger)->logException($e);
            exit;
        }
    }

    /**
     * Retrieves the value of a loaded environment variable.
     *
     * @param string $key Variable name.
     * @param mixed $default Default value if the variable is not found.
     * @return mixed The variable value or the provided default value.
     */
    public static function get(string $key, $default = null)
    {
        return self::$variables[$key] ?? $default;
    }

    /**
     * Set or update an environment variable in the .env file
     *
     * @param string $path
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    public static function set(string $path, string $key, string $value): void
    {
        if (!file_exists($path)) {
            throw new Exception("File .env not found: $path");
        }

        $envContents = file_get_contents($path);

        if (preg_match("/^{$key}=.*/m", $envContents)) {
            $envContents = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContents);
        } else {
            throw new Exception("Environment variable '{$key}' not found in .env file.");
        }

        file_put_contents($path, $envContents);
        self::$variables[$key] = $value;
    }
}
