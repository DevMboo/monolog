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
use Monolog\App\Helpers\Environment\Env;
use Exception;

/**
 * Class ServerStandardCommand
 *
 * This command toggles the STANDARD_MODE value in the .env file between "true" and "false".
 * If STANDARD_MODE is currently set to "true", it changes to "false".
 */
class ServerStandardCommand extends Command 
{
    /**
     * Executes the command to toggle STANDARD_MODE in the environment file.
     *
     * @return void
     */
    public function execute()
    {
        try {
            // Define the path to the .env file
            $envPath = __DIR__ . '/../../../../.env';

            // Load the environment variables from the file
            Env::load($envPath);

            // Get the current value of STANDARD_MODE (default to "false" if not set)
            $currentValue = Env::get('STANDARD_MODE', 'false');

            // Toggle the value: if "true", change to "false"; if "false", change to "true"
            $newValue = ($currentValue === 'true') ? 'false' : 'true';

            // Update the .env file with the new value
            Env::set($envPath, 'STANDARD_MODE', $newValue);

            // Output success message
            $this->info("STANDARD_MODE set to [{$newValue}].");
        } catch (Exception $e) {
            // Handle any errors that occur during execution
            $this->error("Error: " . $e->getMessage());
        }
    }
}
