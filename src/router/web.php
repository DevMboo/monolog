<?php

use Monolog\App\Http\Router;

use Monolog\App\Controller\WelcomeController;

/**
 * web.php - Application Routing File
 *
 * This file is responsible for defining the routes of the application.
 * It maps URLs to their respective controllers and methods, handling different
 * HTTP request types (GET, POST, etc.) and applying middlewares when necessary.
 *
 * Routes are registered using the `Router` class, which provides methods
 * for handling different HTTP verbs and allows grouping routes under a common prefix.
 *
 * Usage:
 * - Define routes using `$router->get()`, `$router->post()`, etc.
 * - Apply authentication or other middleware as needed.
 * - Use `group()` to organize routes under a shared prefix.
 *
 * Example:
 * ```php
 * $router->group('admin', function ($router) {
 *     $router->get('dashboard', [AdminController::class, 'dashboard']);
 *     $router->get('settings', [AdminController::class, 'settings']);
 * });
 * ```
 */

$router = new Router();

/**
 * Define application routes
 */

// Public routes
$router->get('', [WelcomeController::class, 'index'], ['auth']);

/**
 * Grouped Routes Example
 *
 * The `group` method allows for grouping routes under a common prefix.
 * All routes defined within this closure will have the "admin" prefix.
 */

// $router->group('admin', function ($router) {
//     // Routes within this group will have the prefix "admin/"
//     $router->get('dashboard', [HomeController::class, 'dashboard']);
//     $router->get('settings', [HomeController::class, 'settings']);
// });

