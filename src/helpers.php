<?php

if (! function_exists('guzzle')) {
    /**
     * Get a guzzle client instance.
     *
     * @param  string|null  $channel
     * @param  array  $options
     * @return \Jenky\Hermes\Contracts\Hermes
     */
    function guzzle($channel = null, array $options = [])
    {
        return $channel ? app('hermes')->channel($channel, $options) : app('hermes');
    }
}
