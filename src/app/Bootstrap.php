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
namespace Monolog\App;

use Monolog\App\Http\Request;
use Monolog\App\Helpers\Environment\Env;

/**
 * Class Bootstrap
 *
 * Manages the application's bootstrapping process.
 * This class is responsible for initializing the application's core components,
 * loading environment variables, and dispatching HTTP requests.
 */
class Bootstrap {

    /**
     * Handles the application's bootstrapping process.
     *
     * This method is responsible for including the necessary routing files,
     * creating the request object, and dispatching the request to the router
     * for further handling based on the HTTP method and URI.
     */
    public function handle()
    {
        // Loader variebles environment .env path
        Env::load(__DIR__ . '/../../.env');

        // Include the routing file that defines the application routes
        require_once __DIR__ . '/../router/web.php';

        // Create an instance of the Request class to capture incoming request data
        $request = new Request();

        // Dispatch the request to the Router based on the HTTP method and URI
        $router->dispatch($request->method(), $request->uri());
    }
}
