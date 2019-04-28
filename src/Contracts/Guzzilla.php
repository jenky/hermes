<?php

namespace Jenky\Guzzilla\Contracts;

interface Guzzilla
{
    /**
     * Get a service client instance.
     *
     * @param  string  $service
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function service($service = null);
}
