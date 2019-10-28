<?php

namespace Jenky\Hermes\Contracts;

interface Hermes
{
    /**
     * Get a client instance.
     *
     * @param  string  $channel
     * @param  array $options
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function channel($channel = null, array $options = []);
}
