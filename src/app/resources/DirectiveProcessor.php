<?php

namespace Monolog\App\Resources;

/**
 * Class DirectiveProcessor
 * 
 * This class processes custom directives within a given content string.
 * It supports variable replacement, component rendering, loops, conditionals,
 * and switch-case structures.
 */
class DirectiveProcessor {

    // Public path access by directive @assets
    protected string $publicPath = 'src/view/public';

    // Parent directive access private images
    protected string $imagesPath = 'src/view/public/images';

    /**
     * Processes the given content by applying all directive transformations.
     *
     * @param string $content The input content containing directives.
     * @param array $data The data array for variable substitution.
     * @return string The processed content.
     */
    public function process(string $content, array $data): string {
        $content = $this->processVariables($content, $data);
        $content = $this->processComponents($content, $data);
        $content = $this->processLoops($content, $data);
        $content = $this->processConditionals($content, $data);
        $content = $this->processSwitchCases($content, $data);
        $content = $this->processAssets($content);
        $content = $this->processImages($content);
        $content = $this->processErrors($content);
        $content = $this->processCrsfToken($content);
        return $content;
    }

     /**
     * Retrieves the base URL for the application from the environment.
     *
     * This method checks the environment variable `APP_URL` for the base URL
     * of the application. If not found, it falls back to a default value of 
     * `http://localhost`.
     *
     * @return string The base URL of the application.
     */
    private function getBaseUrl(): string {
        // Retrieve the base URL from the environment, fallback to 'http://localhost:PORT' if not set
        $baseUrl = env('APP_URL', 'http://localhost').":".env('APP_PORT', '8000');
        return rtrim($baseUrl, '/'); // Ensure no trailing slash is left
    }

    /**
     * Processes @images directives in the content.
     *
     * This method finds all @images("path") directives and replaces them with
     * the full URL for the image, combining the base URL and images path.
     *
     * @param string $content The input content containing @images directives.
     * @return string The content with processed @images directives.
     */
    protected function processImages(string $content): string {
        return preg_replace_callback('/@images\(["\'](.+?)["\']\)/', function ($matches) {
            // Generate the full URL for the image using the imagesPath
            return $this->getBaseUrl() . '/' . $this->imagesPath . '/' . ltrim($matches[1], '/');
        }, $content);
    }
    
    /**
     * Processes @assets directives in the content.
     *
     * This method finds all @assets("path") directives and replaces them with
     * the full URL for the asset, combining the base URL and public path.
     *
     * @param string $content The input content containing @assets directives.
     * @return string The content with processed @assets directives.
     */
    protected function processAssets(string $content): string {
        return preg_replace_callback('/@assets\(["\'](.+?)["\']\)/', function ($matches) {
            // Generate the full URL for the asset using the publicPath
            return $this->getBaseUrl() . '/' . $this->publicPath . '/' . ltrim($matches[1], '/');
        }, $content);
    }

    /**
     * Processes CSRF token placeholders in the provided content.
     *
     * This method searches for occurrences of `@csrf()` in the given content
     * and replaces them with a hidden input field containing the CSRF token.
     *
     * @param string $content The input content containing `@csrf()` placeholders.
     * @return string The processed content with CSRF tokens injected.
     */
    protected function processCrsfToken(string $content): string {
        return preg_replace_callback('/@csrf\(\)/', function () {
            return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '" />';
        }, $content);
    }
    
    /**
     * Replaces variables in the content with their corresponding values from the data array.
     *
     * @param string $content The content containing variables.
     * @param array $data The data array for substitution.
     * @return string The content with replaced variables.
     */
    protected function processVariables(string $content, array $data): string {
        return preg_replace_callback('/{{\s*(\w+)\s*}}/', function ($matches) use ($data) {
            return $data[$matches[1]] ?? '';
        }, $content);
    }

    /**
     * Processes the @component directives by rendering the specified component.
     *
     * @param string $content The content containing @component directives.
     * @param array $data The data array for substitution.
     * @return string The processed content.
     */
    protected function processComponents(string $content, array $data): string {
        return preg_replace_callback('/@component\(\s*[\'\"](\w+)[\'\"]\s*(?:,\s*(.*))?\)/', function ($matches) {
            $componentName = $matches[1];
            $params = isset($matches[2]) ? $this->parseParams($matches[2]) : [];
            return (new Component())->render($componentName, $params);
        }, $content);
    }

    /**
     * Processes error placeholders in the given content and replaces them with actual error messages.
     * This method scans the provided content for error placeholders of the form `@errors('field_name')`
     * and replaces them with the corresponding error message for the specified field.
     * It retrieves error messages from a session-based validator and ensures the session is started if necessary.
     * 
     * @param string $content The content to be processed, which may contain error placeholders.
     * @return string The content with error placeholders replaced by actual error messages.
     */
    protected function processErrors(string $content): string {
        return preg_replace_callback('/@errors\(["\'](.+?)["\']\)/', function ($matches) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
  
            $validator = new \Monolog\App\Helpers\Validator\Validator([], [], []);
    
            $field = $matches[1];
            $errorMessage = $validator::getError($field);
            
            $validator::forget();

            return $errorMessage ?: '';
        }, $content);
    }
    

    /**
     * Processes @foreach loops in the content.
     *
     * @param string $content The content containing @foreach loops.
     * @param array $data The data array for loop substitution.
     * @return string The processed content.
     */
    protected function processLoops(string $content, array $data): string {
        return preg_replace_callback('/@foreach\s+(\w+)\s+as\s+(\w+)(.*?)@endforeach/s', function ($matches) use ($data) {
            $arrayName = $matches[1];
            $itemName = $matches[2];
            $loopContent = $matches[3];
            $output = '';
            if (!empty($data[$arrayName]) && is_array($data[$arrayName])) {
                foreach ($data[$arrayName] as $item) {
                    $tempContent = $loopContent;
                    foreach ($item as $key => $value) {
                        $tempContent = str_replace("{{ $itemName.$key }}", $value, $tempContent);
                    }
                    $output .= $tempContent;
                }
            }
            return $output;
        }, $content);
    }

    /**
     * Processes @if conditionals in the content.
     *
     * @param string $content The content containing @if statements.
     * @param array $data The data array for conditional evaluation.
     * @return string The processed content.
     */
    protected function processConditionals(string $content, array $data): string {
        return preg_replace_callback('/@if\((.*?)\)(.*?)@endif/s', function ($matches) use ($data) {
            $condition = $this->replaceVariables($matches[1], $data); // Replaces variables
            $ifContent = $matches[2];
    
            $result = $this->evaluateCondition($condition);
            return $result ? $ifContent : '';
        }, $content);
    }
    
    /**
     * Evaluates a conditional expression safely.
     *
     * @param string $condition The condition to evaluate.
     * @return bool The result of the evaluation.
     */
    protected function evaluateCondition(string $condition): bool {
        return eval("return (bool) ($condition);");
    }
    
    /**
     * Processes @switch and @case directives in the content.
     *
     * @param string $content The content containing switch statements.
     * @param array $data The data array for case evaluation.
     * @return string The processed content.
     */
    protected function processSwitchCases(string $content, array $data): string {
        return preg_replace_callback('/@switch\((.*?)\)(.*?)@endswitch/s', function ($matches) use ($data) {
            $variable = $this->replaceVariables($matches[1], $data);
            $cases = $matches[2];
            preg_match_all('/@case\((.*?)\)(.*?)@break/s', $cases, $caseMatches, PREG_SET_ORDER);
            foreach ($caseMatches as $case) {
                if (trim($this->replaceVariables($case[1], $data)) == trim($variable)) {
                    return $case[2];
                }
            }
            return '';
        }, $content);
    }

    /**
     * Replaces variables in expressions with corresponding values from the data array.
     *
     * @param string $expression The expression containing variables.
     * @param array $data The data array for substitution.
     * @return string The expression with replaced variables.
     */
    protected function replaceVariables(string $expression, array $data): string {
        return preg_replace_callback('/\$(\w+)/', function ($matches) use ($data) {
            return $data[$matches[1]] ?? 'null';
        }, $expression);
    }

    /**
     * Parses the parameters passed to the @component directive.
     *
     * @param string $paramString The parameters as a string.
     * @return array An associative array of parameters.
     */
    protected function parseParams(string $paramString): array {
        $params = [];
        preg_match_all('/\s*[\'\"]?(\w+)[\'\"]?\s*:\s*[\'\"]([^\'\"]+)[\'\"]/', $paramString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $params[$match[1]] = $match[2];
        }
        return $params;
    }
}
