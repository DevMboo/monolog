<?php

namespace Monolog\App\Helpers\Debugger;

/**
 * Class Debugger
 *
 * Provides debugging utilities for dumping and displaying values.
 */
class Debugger
{
    /**
     * Dumps the given values in a readable and interactive format.
     *
     * Objects and arrays are displayed inside collapsible <details> tags
     * for better readability.
     *
     * @param mixed ...$values One or more values to be dumped.
     * @return void
     */
    public static function dump(...$values)
    {
        echo "<div style='background: #222; color: #0f0; padding: 10px; border-radius: 5px; font-size: 14px; font-family: monospace;'>";

        foreach ($values as $value) {
            self::renderValue($value);
        }

        echo "</div>";
    }

    /**
     * Recursively renders values with interactive expansion.
     *
     * @param mixed $value The value to be displayed.
     * @param string|null $key The key name (for arrays and objects).
     * @return void
     */
    private static function renderValue($value, $key = null)
    {
        if (is_array($value)) {
            echo "<details style='margin-bottom: 5px;'>";
            echo "<summary style='cursor: pointer; font-weight: bold; color: #ffcc00;'>Array (" . count($value) . ")</summary>";
            echo "<pre style='margin: 5px 0; white-space: pre-wrap; word-wrap: break-word;'>";
            foreach ($value as $k => $v) {
                echo "<strong style='color: #00f;'>[$k]</strong> => ";
                self::renderValue($v, $k);
            }
            echo "</pre>";
            echo "</details>";
        } elseif (is_object($value)) {
            $className = get_class($value);
            echo "<details style='margin-bottom: 5px;'>";
            echo "<summary style='cursor: pointer; font-weight: bold; color: #ff6600;'>Object ($className)</summary>";
            echo "<pre style='margin: 5px 0; white-space: pre-wrap; word-wrap: break-word;'>";
            $properties = (array)$value;
            foreach ($properties as $prop => $val) {
                echo "<strong style='color: #0ff;'>$prop</strong> => ";
                self::renderValue($val, $prop);
            }
            echo "</pre>";
            echo "</details>";
        } elseif (is_string($value)) {
            echo "<span style='color: #0f0;'>\"$value\"</span><br>";
        } elseif (is_int($value) || is_float($value)) {
            echo "<span style='color: #ffcc00;'>$value</span><br>";
        } elseif (is_bool($value)) {
            echo "<span style='color: #ff00ff;'>" . ($value ? 'true' : 'false') . "</span><br>";
        } elseif (is_null($value)) {
            echo "<span style='color: #999;'>null</span><br>";
        } else {
            var_dump($value);
        }
    }

    /**
     * Dumps the given values and terminates script execution.
     *
     * This method calls dump() to display the provided values 
     * and then stops execution using exit.
     *
     * @param mixed ...$values One or more values to be dumped.
     * @return never
     */
    public static function dd(...$values)
    {
        self::dump(...$values);
        exit;
    }
}
