<?php

namespace Monolog\App\Resources;

use Monolog\Exceptions\Exception;

/**
 * Class Component
 *
 * This class is responsible for rendering UI components by fetching their content from 
 * predefined template files and processing any embedded directives.
 *
 * Components allow for modular and reusable UI elements by replacing placeholders 
 * with provided data and processing nested components dynamically.
 */
class Component {
    
    /**
     * The directory path where component template files are stored.
     * 
     * @var string
     */
    protected string $componentPath = __DIR__ . '/../../view/resources/views/components/';
    
    /**
     * Renders the specified component with the given data.
     * 
     * This method loads the corresponding component template file, processes any 
     * embedded directives (such as nested components), and replaces placeholders 
     * with actual data values.
     * 
     * @param string $component The name of the component to render.
     * @param array $data The data to pass to the component.
     * @return string The rendered component content.
     */
    public function render(string $component, array $data = []): string
    {
        try {
            // Path to the component file
            $file = $this->componentPath . $component . '.html';

            // Check if the component file exists
            if (!file_exists($file)) {
                throw new Exception("Component '{$component}' not found!", 404);
            }

            // Get the content of the component file
            $content = file_get_contents($file);

            // Process nested components and directives
            $processor = new DirectiveProcessor();
            $content = $processor->process($content, $data);

            return $content;
        } catch (Exception $e) {
            return $e->render();
        }
    }
}
