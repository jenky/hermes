<?php

if (! function_exists('guzzle')) {
    /**
     * Get a guzzle client instance.
     *
     * @param  string  $connection
     * @param  array  $options
     * @return \Jenky\Elastify\Contracts\ConnectionInterface
     */
    function guzzle($channel = null, array $options = [])
    {
        return $channel ? app('guzzilla') : app('guzzilla')->channel($channel, $options);
    }
}
