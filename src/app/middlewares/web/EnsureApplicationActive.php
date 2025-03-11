<?php 

namespace Monolog\App\Middlewares\Web;

use Monolog\App\Http\Response;

/**
 * Middleware EnsureApplicationActive
 *
 * This middleware checks if the application is in maintenance mode.
 * If maintenance mode is enabled, it returns a 503 response and displays a maintenance page.
 */
class EnsureApplicationActive {

    /**
     * Handles the request and checks if the application is in maintenance mode.
     * 
     * @return bool Returns true if the application is active.
     */
    public function handle()
    {
        // Check if the application is in maintenance mode
        if (filter_var(env('STANDARD_MODE', false), FILTER_VALIDATE_BOOLEAN) === true) {
            $response = new Response();
            $response->setStatusCode(503); // Set HTTP status to 503 (Service Unavailable)
            
            // Display the maintenance page and stop further execution
            echo $this->renderMaintenancePage();
            exit;
        }

        return true; // Continue normal execution if not in maintenance mode
    }

    /**
     * Generates and returns the HTML content for the maintenance page.
     * 
     * @return string The maintenance page HTML.
     */
    private function renderMaintenancePage(): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Maintenance Mode</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    text-align: center; 
                    padding: 50px; 
                    background-color: #f8f9fa; 
                }
                .container {
                    max-width: 600px; 
                    margin: auto; 
                    padding: 20px; 
                    background: #fff;
                    border: 0.3px solid #e1e1e1;
                    border-radius: 10px;
                }
                .code {
                    font-size: 21px;
                    font-weight: bold;
                }
                h1 { color: #dc3545; }
                p { color: #333; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🚧 System in Maintenance Mode 🚧</h1>
                <p class="code">Status 503</p>
                <p>Our system is temporarily offline for improvements.<br>
                Please try again later.</p>
            </div>
        </body>
        </html>
        HTML;
    }
}
