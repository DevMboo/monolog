<?php

namespace Monolog\App\Helpers\Hash;

use Monolog\Exceptions\Exception;

/**
 * Class Hash
 *
 * The Hash class provides methods to generate and verify secure hash values such as MD5, SHA1, and SHA256.
 * This class is useful for encrypting passwords or other sensitive data, ensuring that they are not stored in plain text.
 * The hash function can also be used to verify data integrity and check if hash values match.
 *
 * Available Methods:
 * - hashMD5(string $input): Generates an MD5 hash of the given string.
 * - hashSHA1(string $input): Generates a SHA1 hash of the given string.
 * - hashSHA256(string $input): Generates a SHA256 hash of the given string.
 * - verifyMD5(string $input, string $hash): Verifies if the MD5 hash matches the given string.
 * - verifySHA1(string $input, string $hash): Verifies if the SHA1 hash matches the given string.
 * - verifySHA256(string $input, string $hash): Verifies if the SHA256 hash matches the given string.
 *
 * Example usage:
 * ```php
 * $hash = new Hash();
 * $hashedPassword = $hash->hashSHA256("myPassword");
 * ```
 */
class Hash {

    /**
     * Check if the provided value matches the stored hash by automatically identifying the hash algorithm.
     *
     * This method first inspects the hash format to determine which algorithm was used (bcrypt, Argon2, MD5, SHA256),
     * and then verifies the provided value against the stored hash.
     *
     * @param string $value The value to verify.
     * @param string $hash The stored hash to compare against.
     * @return bool Returns true if the value matches the hash, otherwise false.
     */
    public static function check(string $value, string $hash): bool
    {
        // Check for bcrypt or Argon2 hash by inspecting the hash format
        if (password_get_info($hash)['algoName'] !== 'unknown') {
            return password_verify($value, $hash);  // bcrypt or Argon2
        }

        // Check for MD5 hash (32 characters long)
        if (strlen($hash) === 32) {
            return md5($value) === $hash;  // MD5
        }

        // Check for SHA256 hash (64 characters long)
        if (strlen($hash) === 64) {
            return hash('sha256', $value) === $hash;  // SHA256
        }

        // If the hash format doesn't match any known type, throw an exception
        throw new Exception("Unsupported or unrecognized hash format", 500);
    }

    /**
     * Generate a hash based on the given type and length.
     *
     * @param string $value The value to be hashed.
     * @param string $type The type of hash (bcrypt, argon2i, argon2id, sha256, sha512, md5).
     * @param int|null $length The length of the generated hash (only applicable for non-password hashing algorithms).
     * @return string The hashed value.
     * @throws Monolog\Exceptions\Exception If an unsupported hashing type is provided.
     */
    public static function make(string $value, string $type = 'bcrypt', ?int $length = null): string
    {
        switch (strtolower($type)) {
            case 'bcrypt':
                return password_hash($value, PASSWORD_BCRYPT);
            case 'argon2i':
                return password_hash($value, PASSWORD_ARGON2I);
            case 'argon2id':
                return password_hash($value, PASSWORD_ARGON2ID);
            case 'sha256':
                return substr(hash('sha256', $value), 0, $length ?? 64);
            case 'sha512':
                return substr(hash('sha512', $value), 0, $length ?? 128);
            case 'md5':
                return substr(md5($value), 0, $length ?? 32);
            default:
                throw new Exception("Unsupported hash type: $type", 500);
        }
    }

    /**
     * Generate a bcrypt hash.
     *
     * @param string $value
     * @return string
     */
    public static function bcrypt(string $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    /**
     * Generate an Argon2 hash.
     *
     * @param string $value
     * @return string
     */
    public static function argon2(string $value): string
    {
        return password_hash($value, PASSWORD_ARGON2ID);
    }

    /**
     * Generate an MD5 hash (not recommended for security-sensitive data).
     *
     * @param string $value
     * @return string
     */
    public static function md5(string $value): string
    {
        return md5($value);
    }

    /**
     * Generate a SHA-256 hash.
     *
     * @param string $value
     * @return string
     */
    public static function sha256(string $value): string
    {
        return hash('sha256', $value);
    }

    /**
     * Generate an HMAC hash with a secret key.
     *
     * @param string $value
     * @param string $key
     * @return string
     */
    public static function hmac(string $value, string $key): string
    {
        return hash_hmac('sha256', $value, $key);
    }

    /**
     * Verify a hashed value using bcrypt or Argon2.
     *
     * @param string $value
     * @param string $hash
     * @return bool
     */
    public static function verify(string $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }
}
