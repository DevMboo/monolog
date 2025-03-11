<?php

use Monolog\App\Helpers\Environment\Env;

/**
 * Retrieves the value of an environment variable.
 *
 * This function provides a global helper to access environment variables
 * loaded by the `Env` class. If the requested variable is not found, it
 * returns the specified default value.
 *
 * @param string $key The name of the environment variable.
 * @param mixed $default The default value to return if the variable is not found.
 * @return mixed The environment variable value or the default value.
 */
function env(string $key, $default = null)
{
    return Env::get($key, $default);
}
