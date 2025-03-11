<?php

namespace Monolog\App\Controller;

use Monolog\App\Resources\View;

/**
 * Base Controller Class
 *
 * This abstract class serves as the foundation for all controllers in the application. 
 * It provides common functionality that can be inherited by child controllers, 
 * ensuring code reusability and a structured approach to handling HTTP requests.
 *
 * The primary purpose of this class is to facilitate rendering views, 
 * allowing controllers to return view instances with associated data.
 */
abstract class Controller {

    /**
     * Renders a view and returns the View instance for further manipulation.
     * 
     * This method creates a new instance of the View class, passes the specified 
     * data to it, and returns the instance. This approach allows additional 
     * manipulations (such as layout rendering) before sending the response.
     * 
     * @param string $viewName The name of the view to render.
     * @param array $data Data to be passed to the view.
     * @return View The View instance for further manipulation or layout rendering.
     */
    protected function view(string $viewName, array $data = []): View
    {
        // Create a new View instance
        $view = new View();

        // Render the view with the provided data
        $view->render($viewName, $data);

        // Return the View instance for further manipulation (e.g., layout injection)
        return $view;
    }
}
