<?php

namespace Jenky\Guzzilla;

use Illuminate\Support\Manager;
use Jenky\Guzzilla\Contracts\Guzzilla;

class GuzzleManager extends Manager implements Guzzilla
{
    /**
     * Get a service client instance.
     *
     * @param  string  $service
     * @return \GuzzleHttp\Client
     * @throws \InvalidArgumentException
     */
    public function service($service = null)
    {
        return $this->driver($service);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['guzzilla.default'];
    }
}
