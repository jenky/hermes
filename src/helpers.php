<?php

if (! function_exists('guzzle')) {
    /**
     * Get a guzzle client instance.
     *
     * @param  string  $connection
     * @return \Jenky\Elastify\Contracts\ConnectionInterface
     */
    function guzzle($channel = null)
    {
        return $channel ? app('guzzilla') : app('guzzilla')->channel($channel);
    }
}
