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

/**
 * ServerCommand
 *
 * This command starts the PHP built-in server using the configuration
 * from the .env file. If a custom port is provided using the -port: flag,
 * it overrides the default port.
 */
class ServerCommand extends Command
{
    public function execute()
    {
        // Load environment variables
        $host = Env::get('APP_URL', 'localhost');
        $defaultPort = Env::get('APP_PORT', '8000');

        // Check for custom port argument (-port:NUMBER)
        $customPort = null;
        foreach ($this->args as $arg) {
            if (str_starts_with($arg, '-port:')) {
                $customPort = substr($arg, 6); // Extract the number after -port:
                break;
            }
        }

        // Use custom port if provided, otherwise default from .env
        $port = $customPort ?: $defaultPort;

        // Define the command to start the server
        $command = "php -S {$host}:{$port}";

        $this->info("Starting server on http://{$host}:{$port}...");
        
        // Execute the command
        exec($command);
    }
}
