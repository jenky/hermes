<?php

namespace Jenky\Guzzilla;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use Jenky\Guzzilla\Factory as FactoryContract;

class Factory implements FactoryContract
{
    use ForwardsCalls, Macroable {
        __call as macroCall;
    }

    /**
     * The underlying client implementation.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * Create a new log writer instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  array $config
     * @return void
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Get the underlying client implementation.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Dynamically proxy method calls to the underlying client.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->forwardCallTo($this->client, $method, $parameters);
    }
}
