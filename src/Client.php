<?php

namespace Jenky\Guzzilla;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use Jenky\Guzzilla\Contracts\ResponseHandler;
use Psr\Http\Message\ResponseInterface;

class Client
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
     * The client configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new log writer instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  array $config
     * @return void
     */
    public function __construct(ClientInterface $client, array $config = [])
    {
        $this->client = $client;
        $this->config = $config;
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
     * Map the response to it's handler.
     *
     * @param  mixed $response
     * @return mixed
     */
    protected function mapToResponseHandler($response)
    {
        // Todo: Map the response to custom handler if configured
        if ($response instanceof ResponseInterface
            && is_a($this->config['response']['handler'] ?? null, ResponseHandler::class, true)) {
            // return $this->app->make();
        }

        return $response;
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

        return $this->mapToResponseHandler(
            $this->forwardCallTo($this->client, $method, $parameters)
        );
    }
}
