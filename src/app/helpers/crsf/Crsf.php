<?php

namespace Monolog\App\Helpers\Crsf;

/**
 * CSRF Token Helper Class.
 * 
 * This class helps in generating, storing, and validating CSRF tokens.
 * It ensures that a unique token is stored in the session and can be used
 * for verifying the authenticity of POST requests, preventing Cross-Site
 * Request Forgery (CSRF) attacks.
 * 
 * Features:
 * - Generates a new CSRF token if one does not already exist in the session.
 * - Provides the CSRF token for embedding in forms or for validation.
 * - Validates if the CSRF token provided in a request matches the one stored in the session.
 * - Regenerates the CSRF token every 10 minutes.
 * - Allows for additional data (payload) to be generated and associated with the CSRF token.
 */
class Crsf {

    public string $crsf;

    /**
     * Constructor to initialize the CSRF token.
     * Generates and stores a CSRF token when the class is instantiated.
     */
    public function __construct() {
        // Start the session if it's not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the CSRF token is already in the session
        if (!isset($_SESSION['csrf_token'])) {
            // If not, generate a new token and store it in the session
            $_SESSION['csrf_token'] = $this->generateToken();
            $_SESSION['csrf_token_time'] = time();  // Store the timestamp of token creation
        }

        // Check if the CSRF token is older than 10 minutes (600 seconds)
        if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time'] > 600)) {
            // Regenerate the token and update the timestamp
            $_SESSION['csrf_token'] = $this->generateToken();
            $_SESSION['csrf_token_time'] = time();
        }

        // Set the CSRF token to the class property
        $this->crsf = $_SESSION['csrf_token'];
    }

    /**
     * Generates a new CSRF token.
     * The token is a random string based on the current time and some unique data.
     *
     * @return string The generated CSRF token.
     */
    private function generateToken(): string {
        // Generate a unique token using a combination of the current timestamp and some random data
        return md5(uniqid(rand(), true));
    }

    /**
     * Returns the CSRF token for the form or request.
     *
     * @return string The CSRF token.
     */
    public function getToken(): string {
        return $this->crsf;
    }

    /**
     * Validates the CSRF token.
     * Compares the token provided in the request with the token stored in the session.
     *
     * @param string $token The token received in the request.
     * @return bool Returns true if the tokens match, false otherwise.
     */
    public function validateToken(string $token): bool {
        // Check if the token in the session matches the provided token
        return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
    }

    /**
     * Returns the payload for the CSRF token (additional data you want to include with the token).
     *
     * @return string The payload, based on the current timestamp.
     */
    public function payloadToken(): string {
        // Here you can return additional data (payload) related to the CSRF token if needed
        return md5((new \DateTime())->format('Y-m-d H:i:s'));
    }
}
