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
namespace Monolog\App\Controller;

use Monolog\App\Http\Request;
use Monolog\App\Controller\Controller;

/**
 * -------------------------------------------------------------
 * Base Controller Class
 * -------------------------------------------------------------
 * 
 * This class provides a structured way to handle HTTP requests
 * within the application's MVC architecture.
 * 
 * Controllers process requests, interact with models, and return
 * responses, such as views or JSON data.
 * 
 * Each controller extends the base `Controller` class, which
 * offers shared functionality like middleware support and
 * response handling.
 * 
 */
class WelcomeController extends Controller
{
    /**
     * Default method for handling requests.
     *
     * Typically used to list resources or render a view.
     *
     * @return mixed
     */
    public function index()
    {
        // Render view method
        $this->view('pages/home')->layout('app');
    }
}
