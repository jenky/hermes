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
        return $channel ? app('hermes') : app('hermes')->channel($channel, $options);
    }
}
