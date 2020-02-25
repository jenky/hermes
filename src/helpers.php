<?php

use Jenky\Hermes\Contracts\Hermes;

if (! function_exists('guzzle')) {
    /**
     * Get a guzzle client instance.
     *
     * @param  string|null  $channel
     * @param  array  $options
     * @return \Jenky\Hermes\Contracts\Hermes|\GuzzleHttp\Client
     */
    function guzzle($channel = null, array $options = [])
    {
        return $channel ? app(Hermes::class)->channel($channel, $options) : app(Hermes::class);
    }
}

if (! function_exists('array_merge_recursive_unique')) {
    /**
     * Merges any number of arrays / parameters recursively, using the left array as base, giving priority to the right array. Replacing entries with string keys with values from latter arrays.
     *
     * @param  array[] $arrays
     * @return array
     */
    function array_merge_recursive_unique(...$arrays)
    {
        if (count($arrays) < 2) {
            if ($arrays === []) {
                return [];
            } else {
                return $arrays[0];
            }
        }

        $merged = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) && (isset($merged[$key]) && is_array($merged[$key]))) {
                    $merged[$key] = array_merge_recursive_unique($merged[$key], $value);
                } else {
                    if (is_numeric($key)) {
                        if (! in_array($value, $merged)) {
                            $merged[] = $value;
                        }
                    } else {
                        $merged[$key] = $value;
                    }
                }
            }
            unset($key, $value);
        }

        return $merged;
    }
}
