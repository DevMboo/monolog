<?php

namespace Monolog\App\Resources;

use Monolog\Exceptions\Exception;

/**
 * A View class is responsible for rendering templates and layouts in the system.
 * 
 * It loads view files, processes them with any necessary data, and supports the inclusion of layouts.
 * 
 * The class also allows for processing special directives in view files through the DirectiveProcessor class.
 * It handles the injection of data into the templates and manages the rendering flow.
 * 
 * Attributes:
 * - $viewPath: Defines the base path where the view files are stored.
 * - $data: Holds the data to be passed to the view for rendering.
 * - $content: Stores the rendered content of the view after processing.
 * - $layout: Defines the layout that will wrap the view content, if any.
 * - $directiveProcessor: A handler for processing special directives in the view files.
 */
class View {

    // Path where view files are located
    protected string $viewPath = __DIR__ . '/../../view/resources/views/';
    
    // Data passed to the view for rendering
    protected array $data = [];

    // Holds the content of the view after rendering
    protected ?string $content = null;

    // Holds the layout name to be used for rendering
    protected ?string $layout = null;

    // Processor by specials directive in files rendering
    private ?DirectiveProcessor $directiveProcessor = null;

    public function __construct() {
        $this->directiveProcessor = new DirectiveProcessor;
    }

    /**
     * Magic method for getting the value of protected or private properties.
     * This method is called when accessing a property that is not directly accessible
     * (i.e., a private or protected property).
     *
     * @param string $name The name of the property being accessed.
     * @return mixed The value of the requested property.
     * @throws Exception If the property doesn't exist, an exception can be thrown.
     */
    public function __get($name) {
        // Return the value of the property if it exists
        return $this->{$name};
    }

    /**
     * Magic method for setting the value of protected or private properties.
     * This method is called when setting a value to a property that is not directly
     * accessible (i.e., a private or protected property).
     *
     * @param string $name The name of the property being set.
     * @param mixed $value The value to assign to the property.
     * @return void
     * @throws Exception If the property doesn't exist, an exception can be thrown.
     */
    public function __set($name, $value) {
        // Set the value of the property if it exists
        $this->{$name} = $value;
    }

    /**
     * Renders a view with the given data.
     * 
     * @param string $view The name of the view to render
     * @param array $data The data to pass to the view
     * @return $this Returns the instance for method chaining
     */
    public function render(string $view, array $data = []) {
        $this->data = $data;
        $content = $this->loadTemplate($view);
        $content = $this->directiveProcessor->process($content, $this->data);

        if ($this->layout) {
            $content = $this->renderLayout($content);
        }

        $this->content = $content;
        return $this;
    }

    /**
     * Loads the view template file.
     * 
     * @param string $viewName The name of the view file to load
     * @return string The content of the view file
     * @throws Monolog\Exceptions\Exception If the view file doesn't exist
     */
    public function loadTemplate(string $viewName): string {
        try {
            $filePath = $this->viewPath . $viewName . '.html';
    
            if (!file_exists($filePath)) {
                throw new Exception("View '{$viewName}' not found at path: {$filePath}", 404);
            }
    
            return file_get_contents($filePath);
        } catch (Exception $e) {
            return $e->render();
        }
    }

    /**
     * Sets the layout to be used for the view.
     * 
     * @param string $layoutName The layout file to use.
     * @return void
     * @throws Exception If the layout file does not exist.
     */
    public function layout(string $layoutName) {
        try {
            // Define the path to the layout file
            $layoutPath = __DIR__ . '/../../view/resources/views/' . $layoutName . '.html';
    
            // Check if the layout file exists
            if (!file_exists($layoutPath)) {
                throw new Exception("Layout '{$layoutName}' not found at path: {$layoutPath}", 404);
            }
    
            // Load the layout file content
            $content = file_get_contents($layoutPath);
    
            // Ensure $this->content is defined before replacing
            $viewContent = $this->content ?? '';
    
            // Replace the {{slot}} placeholder with the actual view content
            $content = str_replace('{{slot}}', $viewContent, $content);
    
            // Process specials vars at file HTML
            $content = $this->directiveProcessor->process($content, $this->data);
    
            // Return the final layout content
            echo $content;
                    
        } catch (Exception $e) {
            // Use the custom exception rendering to display the error
            return $e->render();
        }
    }
    
    /**
     * Renders the layout by replacing the {{slot}} placeholder with the view content.
     * 
     * @param string $content The rendered content of the view
     * @return string The layout content with the view content injected
     */
    protected function renderLayout(string $content) {
        // Load the layout content
        $layoutContent = $this->loadTemplate($this->layout);

        // Replace the {{slot}} placeholder with the actual content
        return str_replace('{{slot}}', $content, $layoutContent);
    }


}
