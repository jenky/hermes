<?php

namespace Jenky\Hermes;

/**
 * Merges any number of arrays / parameters recursively, using the left array as base, giving priority to the right array. Replacing entries with string keys with values from latter arrays.
 *
 * @param  array[] $arrays
 * @return array
 */
function array_merge_recursive_distinct(...$arrays)
{
    if (count($arrays) < 2) {
        return empty($arrays) ? [] : $arrays[0];
    }

    $merged = array_shift($arrays);

    foreach ($arrays as $array) {
        foreach ($array as $key => $value) {
            if (is_array($value) && (isset($merged[$key]) && is_array($merged[$key]))) {
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
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
