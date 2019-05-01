<?php

namespace Jenky\Guzzilla;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Traits\ForwardsCalls;
use Psr\Http\Message\ResponseInterface;

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
     * Map the response to it's handler.
     *
     * @param  mixed $response
     * @return mixed
     */
    protected function mapToResponseHandler($response)
    {
        // Todo: Map the response to custom handler if configured
        // if ($response instanceof ResponseInterface) {
        //     return ;
        // }

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
        return $this->mapToResponseHandler(
            $this->forwardCallTo($this->client, $method, $parameters)
        );
    }
}
