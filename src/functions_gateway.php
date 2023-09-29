<?php

if ( ! function_exists('source_path'))
{
    /**
     * Get the Source path.
     *
     * @param  string $path
     * @return string
     */
    function source_path($path = '')
    {
        return app()->basePath() . '/src' . ($path ? '/' . $path : $path);
    }
}


if (! function_exists('app_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app()->basePath() . '/app' . ($path ? '/' . $path : $path);
    }
}

if (! function_exists('app_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app()->basePath() . '/app' . ($path ? '/' . $path : $path);
    }
}

if (! function_exists('key_replace')) {
    /**
     * Replace the specified array key using dot notation.
     *
     * @param  string $originalKey  The original key in dot notation to be replaced.
     * @param  string $newKey       The new key to replace the original key.
     * @param  array  &$array       The array to modify.
     * @return void
     */
    function key_replace($originalKey, $newKey, &$array) {
        $keys = explode('.', $originalKey);

        // Navigate the array to find the original key
        $temp = &$array;
        foreach ($keys as $idx => $key) {
            if ($idx === count($keys) - 1) {
                // If we are at the last key, reassign
                $temp[$newKey] = $temp[$key];
                unset($temp[$key]);
            } else {
                // If not the last key, keep navigating
                $temp = &$temp[$key];
            }
        }
    }
}