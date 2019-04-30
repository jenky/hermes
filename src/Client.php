<?php

namespace Jenky\Guzzilla;

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Traits\ForwardsCalls;

class Client
{
    use ForwardsCalls;

    /**
     * The underlying logger implementation.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * Create a new log writer instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  \Illuminate\Contracts\Events\Dispatcher|null  $dispatcher
     * @return void
     */
    public function __construct(ClientInterface $client, Dispatcher $dispatcher = null)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher;
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
     * Dynamically proxy method calls to the underlying logger.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->client, $method, $parameters);
    }
}
