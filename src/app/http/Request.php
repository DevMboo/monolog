<?php

namespace Monolog\App\Http;

use \Monolog\App\Http\Response;
use Monolog\App\Helpers\Validator\Validator;
use Monolog\Exceptions\ValidationException;

/**
 * The Request class handles the HTTP request data such as query parameters, body parameters, headers, and cookies.
 * It provides methods to retrieve these data in a structured way, making it easier to handle incoming requests.
 */
class Request {

    // Arrays to store different types of parameters from the request
    protected array $vars = [];
    protected array $queryParams = [];
    protected array $bodyParams = [];
    protected array $headers = [];
    protected array $cookies = [];
    protected array $files = [];

    /**
     * Request constructor.
     *
     * The constructor parses the route parameters, query parameters, body parameters,
     * headers, and cookies from the incoming HTTP request and stores them in the corresponding arrays.
     */
    public function __construct() {
        $this->vars = $this->parseRouteParams();  // Parses dynamic route parameters
        $this->queryParams = $_GET ?? [];  // Captures query parameters (from URL)
        $this->bodyParams = $this->parseBody();  // Captures the body data (POST or JSON)
        $this->headers = $this->getHeaders();  // Captures HTTP headers
        $this->cookies = $_COOKIE ?? [];  // Captures cookies
        $this->files = $_FILES ?? []; // Captures files request
    }

    /**
     * Validates the given data based on the provided rules and messages.
     *
     * This method checks if any validation rules are provided. If so, it creates a validator 
     * to validate the data. If the validation fails, it redirects the user back to the 
     * previous page and stops further execution.
     *
     * @param array $data The data to be validated.
     * @param array $rules Validation rules for the data.
     * @param array $messages Custom validation messages.
     * @return array The original data if validation passes or if no rules are provided.
     * 
     */
    public function validate(array $rules = [], array $messages = [])
    {
        $data = $this->bodyParams;

         // Check if validation rules are provided
        if (!empty($rules)) {
            // Perform validation only if rules are provided
            $validator = Validator::make($data, $rules, $messages);

            // If validation fails, redirect the user back to the previous page
            if ($validator->fails()) {
                // Create a new Response instance
                $response = new Response();
                
                // Redirect back to the previous page
                $response->back();

                // Stop further script execution
                exit;
            }

            // Clears the error session if the request rules is correct
            Validator::forget();
        }

        // Return the data if no rules are provided or validation passes
        return $data;
    }

    /**
     * Retrieves all the files uploaded with the request.
     *
     * This method returns an array containing all the files uploaded via the
     * request, typically through a form with `enctype="multipart/form-data"`.
     * Each file will be represented as an associative array containing details
     * such as the file name, temporary path, MIME type, and any upload errors.
     *
     * @return array An associative array of uploaded files, where the keys are the
     *               field names from the form and the values are arrays containing
     *               file details (e.g., 'name', 'tmp_name', 'size', 'type', 'error').
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * Returns the full URL of the request (including the domain and query string).
     *
     * @return string The complete URL of the current request
     */
    public function url(): string
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Returns the URI (path) of the request, without the domain and query string.
     *
     * @return string The URI path of the current request
     */
    public function uri(): string
    {
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }

    /**
     * Returns the HTTP method of the request (e.g., GET, POST, PUT, DELETE).
     *
     * @return string The HTTP method used for the current request
     */
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Returns a parameter captured from the URL (for dynamic routes).
     *
     * @param string $key The name of the parameter
     * @param mixed $default The default value if the parameter is not set
     * @return mixed The value of the parameter or the default value
     */
    public function param(string $key, mixed $default = null): mixed
    {
        return $this->vars[$key] ?? $default;
    }

    /**
     * Returns a GET parameter from the query string.
     *
     * @param string $key The name of the GET parameter
     * @param mixed $default The default value if the parameter is not set
     * @return mixed The value of the GET parameter or the default value
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    /**
     * Returns a parameter from the request body (either from POST or JSON data).
     *
     * @param string $key The name of the body parameter
     * @param mixed $default The default value if the parameter is not set
     * @return mixed The value of the body parameter or the default value
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->bodyParams[$key] ?? $default;
    }

    /**
     * Returns all parameters from the request, merging query parameters and body parameters.
     *
     * @return array All query parameters and body parameters in a combined array
     */
    public function all(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }

    /**
     * Returns an HTTP header.
     *
     * @param string $key The name of the header
     * @param mixed $default The default value if the header is not set
     * @return mixed The value of the header or the default value
     */
    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers[$key] ?? $default;
    }

    /**
     * Returns a cookie value.
     *
     * @param string $key The name of the cookie
     * @param mixed $default The default value if the cookie is not set
     * @return mixed The value of the cookie or the default value
     */
    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Retrieves all HTTP headers from the request.
     *
     * @return array An associative array of HTTP headers
     */
    protected function getHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            // Headers are stored in $_SERVER with a "HTTP_" prefix
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace('_', '-', substr($key, 5));  // Convert to proper header format
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    /**
     * Parses the body of the request (supports POST and JSON).
     *
     * @return array The parsed body parameters
     */
    protected function parseBody(): array
    {
        if ($this->method() === 'POST') {
            return $_POST;  // For POST requests, capture the form data
        }

        // For other methods (e.g., PUT), capture JSON body data
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        return is_array($data) ? $data : [];  // Return parsed JSON if it's an array
    }

    /**
     * Parses dynamic route parameters (e.g., {id}, {slug}).
     *
     * @return array An array of route parameters
     */
    protected function parseRouteParams(): array
    {
        $vars = [];
        // This method can be enhanced in the Router class to capture dynamic route parameters
        return $vars;
    }
}
