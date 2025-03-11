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
namespace Monolog\App\Console;

require_once __DIR__ . '/../helpers/common/env.php';

/**
 * Base Command class for CLI execution.
 * 
 * This class serves as the foundation for all console commands.
 * It provides basic functionality for handling command-line arguments
 * and displaying messages with different formats.
 */
class Command {
    /**
     * @var array $args Stores the arguments passed to the command.
     */
    protected array $args = [];

    /**
     * Command constructor.
     * 
     * @param array $args The arguments received from the command line.
     */
    public function __construct($args) {
        $this->args = $args;
    }

    /**
     * Displays an informational message in green color.
     * 
     * @param string $message The message to display.
     */
    public function info($message) {
        echo "\033[32m$message\033[0m\n";
    }

    /**
     * Displays an error message in red color.
     * 
     * @param string $message The error message to display.
     */
    public function error($message) {
        echo "\033[31m$message\033[0m\n";
    }
}
