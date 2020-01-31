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
