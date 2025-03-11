<?php

use Monolog\App\Helpers\Debugger\Debugger;

/**
 * Dumps the given values in a readable format.
 *
 * This function calls the Debugger::dump method to display the contents
 * of the provided values with proper formatting for easier debugging.
 *
 * @param mixed ...$values One or more values to be dumped.
 * @return void
 */
function dump(...$values)
{
    Debugger::dump(...$values);
}

/**
 * Dumps the given values and stops script execution.
 *
 * This function calls the Debugger::dd method, which first dumps
 * the provided values and then terminates the script execution.
 *
 * @param mixed ...$values One or more values to be dumped.
 * @return never
 */
function dd(...$values)
{
    Debugger::dd(...$values);
}
