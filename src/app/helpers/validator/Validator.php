<?php

namespace Monolog\App\Helpers\Validator;

/**
 * Class Validator
 *
 * This class is responsible for validating input data based on predefined rules.
 * It checks if the provided data meets the specified validation criteria and 
 * stores any validation errors encountered during the process.
 * 
 * Features:
 * - Supports various validation rules (e.g., required, email, integer, min, max, file).
 * - Allows custom error messages.
 * - Stores validation errors in a session for later retrieval.
 * - Provides methods to check if validation failed and to retrieve errors.
 */
class Validator
{
    /**
     * The input data to be validated.
     *
     * @var array
     */
    protected array $data;

    /**
     * The validation rules for the fields.
     *
     * @var array
     */
    protected array $rules;

    /**
     * Custom error messages for validation failures.
     *
     * @var array
     */
    protected array $messages;

    /**
     * Stores validation errors.
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Validator constructor.
     *
     * @param array $data The data to be validated.
     * @param array $rules The validation rules.
     * @param array $messages (Optional) Custom error messages.
     */
    public function __construct(array $data, array $rules, array $messages = [])
    {
        // Start the session if it's not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->validate();
    }

    /**
     * Creates a new instance of the Validator.
     *
     * @param array $data The data to be validated.
     * @param array $rules The validation rules.
     * @param array $messages (Optional) Custom error messages.
     *
     * @return Validator
     */
    public static function make(array $data, array $rules, array $messages = []): self
    {
        return new self($data, $rules, $messages);
    }

    /**
     * Executes validation based on the defined rules.
     */
    protected function validate(): void
    {
        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;
            $rulesArray = explode('|', $rules);

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        if (!empty($this->errors)) {
            $_SESSION['errors'] = $this->errors; // Stores errors in session.
        }
    }

    /**
     * Applies a validation rule to a given field.
     *
     * @param string $field The name of the field being validated.
     * @param mixed $value The value of the field.
     * @param string $rule The validation rule to be applied (e.g., "required", "email", "min:3").
     *
     * @return void
     */
    protected function applyRule(string $field, mixed $value, string $rule): void
    {

        if (str_contains($rule, ':')) {
            [$ruleName, $parameter] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        $messageKey = "{$field}.{$ruleName}";

        switch ($ruleName) {
            // 'required': Checks if the field is not empty.
            case 'required':
                if (empty($value)) {
                    $this->addError($field, $messageKey, "The field $field is required.");
                }
                break;
        
            // 'email': Validates if the value is a valid email address using PHP's filter.
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, $messageKey, "The field $field must be a valid email address.");
                }
                break;
        
            // 'integer': Validates if the value is a valid integer using PHP's filter.
            case 'integer':
                if (!filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, $messageKey, "The field $field must be an integer.");
                }
                break;
                        
            // 'string': Validates if the value is a string.
            case 'string':
                if (!is_string($value)) {
                    $this->addError($field, $messageKey, "The field $field must be a string.");
                }
                break;
        
            // 'min': Checks if the value is greater than or equal to a specified minimum value.
            case 'min':
                if (strlen($value) < (int) $parameter) {
                    $this->addError($field, $messageKey, "The field $field must be at least $parameter.");
                }
                break;
        
            // 'max': Checks if the value is less than or equal to a specified maximum value.
            case 'max':
                if (strlen($value) > (int) $parameter) {
                    $this->addError($field, $messageKey, "The field $field must be at most $parameter.");
                }
                break;
        
            // 'file': Checks if a file has been uploaded and there were no errors during the upload.
            case 'file':
                if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                    $this->addError($field, $messageKey, "The field $field must be a valid file.");
                }
                break;
        
            // 'mimes': Checks if the uploaded file has an allowed MIME type based on the given parameter.
            case 'mimes':
                if (isset($_FILES[$field])) {
                    $allowedTypes = explode(',', $parameter);
                    $fileType = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                    if (!in_array($fileType, $allowedTypes)) {
                        $this->addError($field, $messageKey, "The field $field must be a file of type: $parameter.");
                    }
                }
                break;
        
            // 'max_size': Checks if the uploaded file size is less than or equal to the specified size in kilobytes.
            case 'max_size':
                if (isset($_FILES[$field]) && $_FILES[$field]['size'] > ((int) $parameter * 1024)) {
                    $this->addError($field, $messageKey, "The field $field must not exceed $parameter KB.");
                }
                break;
        
            default:
                break;
        }
        
    }

    /**
     * Adds an error message to the errors array.
     *
     * @param string $field The name of the field that failed validation.
     * @param string $key The key used to reference the error message, typically in the format "{field}.{rule}".
     * @param string $defaultMessage The default error message to be used if no custom message is provided.
     *
     * @return void
     */
    protected function addError(string $field, string $key, string $defaultMessage): void
    {
        $message = $this->messages[$key] ?? $defaultMessage;
        $this->errors[$field][] = $message;
    }

    /**
     * Checks if validation has failed.
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Retrieves validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Clears the errors from the session.
     *
     * @return void
     */
    public static function forget(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION['errors']);
        }
    }

    /**
     * Retrieves an error message from the session.
     *
     * @param string $field The name of the field for which to retrieve the error message.
     *
     * @return string|null
     */
    public static function getError(string $field): ?string
    {
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['errors'][$field])) {
            return $_SESSION['errors'][$field][0] ?? null;
        }
        return null;
    }
}
