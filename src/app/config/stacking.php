<?php 

// This file declares the middlewares for the application
// It defines the default middlewares as well as the middlewares for specific routes (like web routes)
return [
    // Default middleware configuration for the application
    'default' => [
        // Middleware to ensure the application is active (probably checks if the app is up and running)
        'online' => \Monolog\App\Middlewares\Web\EnsureApplicationActive::class,

        // Middleware to ensure that the CSRF token is valid and provided for security
        'csrf' => \Monolog\App\Middlewares\Web\EnsureApplicationCrsfToken::class,
    ],

    // Middleware configuration for web-related routes
    'web' => [
        // Middleware to ensure the user is authenticated, checks if there's a valid session
        'auth' => \Monolog\App\Middlewares\Web\EnsureAutenticateSessionUser::class
    ],

    // Specials middlewares register by API application
    'api' => []
];
