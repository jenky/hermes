<?php

namespace Jenky\Guzzilla\Events;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestHandled
{
    /**
     * The request instance.
     *
     * @var \Psr\Http\Message\RequestInterface
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \Psr\Http\Message\ResponseInterface|null
     */
    public $response;

    /**
     * The request options.
     *
     * @var array
     */
    public $options = [];

    /**
     * Create a new event instance.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Psr\Http\Message\ResponseInterface|null $response
     * @param  array $options
     * @return void
     */
    public function __construct(RequestInterface $request, ?ResponseInterface $response = null, array $options = [])
    {
        $this->request = $request;
        $this->response = $response;
        $this->options = $options;
    }
}
