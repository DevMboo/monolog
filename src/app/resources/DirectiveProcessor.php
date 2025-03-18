<?php

namespace Monolog\App\Resources;

/**
 * Class DirectiveProcessor
 * 
 * This class processes custom directives within a given content string.
 * It supports variable replacement, component rendering, loops, conditionals,
 * and switch-case structures.
 */
class DirectiveProcessor
{

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
    public function process(string $content, array $data): string
    {
        $content = $this->processMessage($content);
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
    private function getBaseUrl(): string
    {
        // Retrieve the base URL from the environment, fallback to 'http://localhost:PORT' if not set
        $baseUrl = env('APP_URL', 'http://localhost') . ":" . env('APP_PORT', '8000');
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
    protected function processImages(string $content): string
    {
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
    protected function processAssets(string $content): string
    {
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
    protected function processCrsfToken(string $content): string
    {
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
    protected function processVariables(string $content, array $data): string
    {
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
    protected function processComponents(string $content, array $data): string
    {
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
    protected function processErrors(string $content): string
    {
        return preg_replace_callback('/@errors\(["\'](.+?)["\']\)/', function ($matches) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $validator = new \Monolog\App\Helpers\Validator\Validator([], [], []);

            $field = $matches[1];
            $errorMessage = $validator::getError($field);

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
    protected function processLoops(string $content, array $data): string
    {
        return preg_replace_callback('/@foreach\s+(\w+)\s+as\s+(\w+)(.*?)@endforeach/s', function ($matches) use ($data) {
            // Capture the array name and the item variable from the foreach directive
            $arrayName = $matches[1]; // Name of the array variable
            $itemName = $matches[2];  // Name of the item variable in the loop
            $loopContent = $matches[3]; // Content inside the @foreach and @endforeach
            $output = ''; // Variable to store the generated HTML

            // Check if the array exists in the data and is actually an array
            if (!empty($data[$arrayName]) && is_array($data[$arrayName])) {
                foreach ($data[$arrayName] as $item) { // Iterate over the array items
                    $tempContent = $loopContent; // Store the loop content temporarily

                    // If `$item` is a string or number, convert it into an associative array
                    if (!is_array($item)) {
                        $item = ['value' => $item]; // Create an array with a 'value' key
                    }

                    // Replace variables inside the loop content
                    foreach ($item as $key => $value) {
                        $tempContent = str_replace("{{ $itemName.$key }}", $value, $tempContent);
                    }

                    // Append the processed content to the final output
                    $output .= $tempContent;
                }
            }

            return $output; // Return the rendered content
        }, $content);
    }

    /**
     * Processes @if conditionals in the content.
     *
     * @param string $content The content containing @if statements.
     * @param array $data The data array for conditional evaluation.
     * @return string The processed content.
     */
    protected function processConditionals(string $content, array $data): string
    {
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
    protected function evaluateCondition(string $condition): bool
    {
        return eval("return (bool) ($condition);");
    }

    /**
     * Processes @switch and @case directives in the content.
     *
     * This function replaces @switch statements with the corresponding @case content 
     * based on the provided data. It also ensures that any extra quotation marks 
     * around the values are removed to allow accurate comparisons.
     *
     * @param string $content The content containing switch statements.
     * @param array $data The data array used for case evaluation.
     * @return string The processed content with the correct case value.
     */
    protected function processSwitchCases(string $content, array $data): string
    {
        return preg_replace_callback('/@switch\((.*?)\)(.*?)@endswitch/s', function ($matches) use ($data) {
            // Extract and replace the variable inside @switch
            $variable = $this->replaceVariables($matches[1], $data);
            $cases = $matches[2];

            // Remove extra single or double quotes from the variable
            $variable = trim($variable, '"\''); 

            // Find all @case statements
            preg_match_all('/@case\((.*?)\)(.*?)@break/s', $cases, $caseMatches, PREG_SET_ORDER);
            $defaultCase = null; // To store @default content if present

            // Loop through each @case statement
            foreach ($caseMatches as $case) {
                // Replace variables inside @case conditions
                $caseValue = $this->replaceVariables($case[1], $data);

                // Remove extra single or double quotes from the @case value
                $caseValue = trim($caseValue, '"\'');

                // Compare the values after trimming spaces and removing quotes
                if (trim($caseValue) == trim($variable)) {
                    return $case[2]; // Return the matched @case content
                }
            }

            // If no @case matches, check for @default content
            if (preg_match('/@default(.*?)@endswitch/s', $cases, $defaultMatch)) {
                $defaultCase = $defaultMatch[1]; // Store @default content
            }

            // Return the @default content if found, otherwise return an empty string
            return $defaultCase ?? '';
        }, $content);
    }

    /**
     * Replaces variables in expressions with corresponding values from the data array.
     *
     * @param string $expression The expression containing variables.
     * @param array $data The data array for substitution.
     * @return string The expression with replaced variables.
     */
    protected function replaceVariables(string $expression, array $data): string
    {
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
    protected function parseParams(string $paramString): array
    {
        $params = []; // Initialize an empty array to store the parsed parameters
        
        // Regular expression to capture key-value pairs, including arrays
        preg_match_all('/\s*[\'"](\w+)[\'"]\s*:\s*(\[[^\]]*\]|[\'\"][^\'\"]*[\'\"])/', $paramString, $matches, PREG_SET_ORDER);

        // Iterate through the matches found by the regular expression
        foreach ($matches as $match) {
            $key = $match[1]; // The key is the first captured group (parameter name)
            $value = trim($match[2]); // The value is the second captured group, trimming extra spaces

            // Check if the value is an array
            if (preg_match('/^\[.*\]$/', $value)) {
                // If it's an array, remove the brackets and convert the comma-separated values into a real array
                $value = array_map(fn($v) => trim($v, " '\""), explode(',', trim($value, '[]'))); // Remove quotes and spaces from each element
            } else {
                // Otherwise, treat the value as a string
                $value = trim($value, "'\""); // Remove surrounding quotes
            }

            $params[$key] = $value; // Store the key-value pair in the $params array
        }

        return $params; // Return the parsed parameters array
    }

    /**
     * Processes @message directives in the content.
     *
     * This method finds all @message('session_name') directives and replaces them
     * with the message stored in the session under the specified session name.
     *
     * @param string $content The input content containing @message directives.
     * @return string The content with processed @message directives.
     */
    protected function processMessage(string $content): string
    {
        return preg_replace_callback('/@message\(["\'](.+?)["\']\)(.*?)@endmessage/s', function ($matches) {
            // Retrieve the session name
            $sessionName = $matches[1];

            // Retrieve the message from the session
            $message = $this->getMessage($sessionName);

            // Check if there is a message in the session
            if ($message !== null) {
                // Get the content inside the @message...@endmessage block
                $blockContent = $matches[2];

                // Replace the {{ message }} inside the content with the session message
                $processedContent = preg_replace('/\{\{\s*message\s*\}\}/', $message, $blockContent);

                // Return the processed content with the message replaced
                return $processedContent;
            }

            // If there is no message, return an empty string
            return '';
        }, $content);
    }


    /**
     * Gets a flash message from the session.
     *
     * @param string $name
     * @return string|null
     */
    protected function getMessage(string $name): ?string
    {
        // Check if the $_SESSION variable exists and start the session if necessary
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the message is in the session
        if (!isset($_SESSION['messages'][$name])) {
            return null;
        }

        // Retrieve the message and remove it from the session after use
        $message = $_SESSION['messages'][$name];
        unset($_SESSION['messages'][$name]); // Remove after use

        return $message;
    }
}
