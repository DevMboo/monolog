<?php

namespace Monolog\App\Http;

use Closure;

use Monolog\App\Http\Response;
use Monolog\App\Middlewares\Middlewares;
use Monolog\Exceptions\Exception;
use Monolog\Exceptions\Logger;

/**
 * A Router class is responsible for handling HTTP routing in the application.
 * It allows defining routes for various HTTP methods (GET, POST, PUT, DELETE)
 * and associates these routes with specific callbacks (such as controllers or closures).
 * The Router also supports middleware functionality and grouping of routes under a common prefix.
 * 
 * Main Responsibilities:
 * - Registering routes for different HTTP methods.
 * - Grouping routes under a shared URL prefix.
 * - Dispatching the appropriate callback for the matched route.
 * - Handling middlewares associated with routes.
 * - Matching a request to the correct route based on HTTP method and URI.
 */
class Router
{
    protected array $routes = [];
    protected string $groupPrefix = '';

    /**
     * Registers a GET route with an optional set of middlewares.
     *
     * @param string $route The route path.
     * @param mixed $callback The callback (controller or closure) to be executed when the route is matched.
     * @param array $middlewares The middlewares to be applied to this route.
     */
    public function get(string $route, $callback, $middlewares = [])
    {
        $this->addRoute('GET', $route, $callback, $middlewares);
    }

    /**
     * Registers a POST route with an optional set of middlewares.
     *
     * @param string $route The route path.
     * @param mixed $callback The callback (controller or closure) to be executed when the route is matched.
     * @param array $middlewares The middlewares to be applied to this route.
     */
    public function post(string $route, $callback, $middlewares = [])
    {
        $this->addRoute('POST', $route, $callback, $middlewares);
    }

    /**
     * Registers a PUT route with an optional set of middlewares.
     *
     * @param string $route The route path.
     * @param mixed $callback The callback (controller or closure) to be executed when the route is matched.
     * @param array $middlewares The middlewares to be applied to this route.
     */
    public function put(string $route, $callback, $middlewares = [])
    {
        $this->addRoute('PUT', $route, $callback, $middlewares);
    }

    /**
     * Registers a DELETE route with an optional set of middlewares.
     *
     * @param string $route The route path.
     * @param mixed $callback The callback (controller or closure) to be executed when the route is matched.
     * @param array $middlewares The middlewares to be applied to this route.
     */
    public function delete(string $route, $callback, $middlewares = [])
    {
        $this->addRoute('DELETE', $route, $callback, $middlewares);
    }

    /**
     * Adds a route to the router's collection, including method, route, callback, and middlewares.
     *
     * @param string $method The HTTP method (GET, POST, etc.).
     * @param string $route The route path.
     * @param mixed $callback The callback (controller or closure) to be executed when the route is matched.
     * @param array $middlewares The middlewares to be applied to this route.
     */
    protected function addRoute(string $method, string $route, $callback, $middlewares = [])
    {
        // If a group prefix is set, prepend it to the route
        if ($this->groupPrefix) {
            $route = $this->groupPrefix . '/' . $route;
        }

        // Add the route to the collection
        $this->routes[$method][$route] = [
            'callback' => $callback,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Groups routes under a common prefix, allowing you to define several routes at once.
     *
     * @param string $prefix The prefix to apply to all routes within the group.
     * @param Closure $callback The callback that defines the routes within the group.
     */
    public function group(string $prefix, Closure $callback)
    {
        // Save the previous group prefix and set the new one
        $previousPrefix = $this->groupPrefix;
        $this->groupPrefix = $prefix;

        // Execute the callback to add the routes within the group
        $callback($this);

        // Restore the previous prefix after the group
        $this->groupPrefix = $previousPrefix;
    }

    /**
     * Dispatches the request by matching the method and URI to a registered route,
     * then executes the associated middlewares and callback.
     *
     * @param string $method The HTTP method of the request.
     * @param string $uri The URI of the request.
     */
    public function dispatch($method, $uri)
    {
        try {
            // Try to match the route for the given method and URI
            [$matchedRoute, $params] = $this->matchRoute($method, $uri);
    
            // If no route is matched, return a "route not found" message
            if (!$matchedRoute) {
                throw new Exception("Route not found for $method $uri", 404);
            }
    
            // Execute any middlewares associated with the matched route
            $this->handleMiddlewares($matchedRoute['middlewares']);
            
            // Execute the route's callback with any matched parameters
            $this->executeCallback($matchedRoute['callback'], $params);
        } catch (\Throwable $e) {
            (new Response)->handleException($e);
            (new Logger)->logException($e);
            exit;
        }
    }

    /**
     * Matches the provided method and URI to a route in the collection.
     *
     * @param string $method The HTTP method of the request.
     * @param string $uri The URI of the request.
     * 
     * @return array An array containing the matched route data and parameters.
     */
    protected function matchRoute($method, $uri)
    {
        // Iterate through the routes for the given method
        foreach ($this->routes[$method] as $routePattern => $routeData) {
            // Escape the route pattern for use in a regular expression
            $escapedPattern = preg_quote($routePattern, '#');

            // Replace route parameters (e.g. {id}) with regex capture groups
            $pattern = preg_replace('#\\\{([^}]+)\\\}#', '([^/]+)', $escapedPattern);

            // If the pattern matches the URI, return the route data and parameters
            if (preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches); // Remove the full match
                return [$routeData, $matches]; // Return route data and matched parameters
            }
        }

        // If no match is found, return null and an empty array
        return [null, []];
    }

    /**
     * Executes the middlewares associated with the given route.
     *
     * @param array $middlewares The list of middlewares to execute.
     */
    protected function handleMiddlewares(array $middlewares)
    {
        // Iterate through the middlewares and execute each one
        foreach ($middlewares as $middleware) {
            (new Middlewares)->execute($middleware);
        }
    }

    /**
     * Executes the callback associated with the matched route.
     *
     * @param mixed $callback The callback (controller or closure) to execute.
     * @param array $params The parameters to pass to the callback.
     */
    protected function executeCallback($callback, array $params)
    {
        // If the callback is an array (controller and method), call the controller method
        if (is_array($callback)) {
            [$controller, $method] = $callback;
            $controllerInstance = new $controller();
            call_user_func_array([$controllerInstance, $method], $params);
        } elseif ($callback instanceof Closure) {
            // If the callback is a closure, execute it
            call_user_func_array($callback, $params);
        }
    }
}
