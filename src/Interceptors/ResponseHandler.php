<?php

namespace Jenky\Hermes\Interceptors;

use InvalidArgumentException;
use Jenky\Hermes\Contracts\ResponseHandler as ResponseHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseHandler
{
    /**
     * The default response handler class name.
     *
     * @var string
     */
    protected $response;

    /**
     * Create a new handler instance.
     *
     * @param  string|null $response
     * @return void
     */
    public function __construct($response = null)
    {
        $this->response = $response;
    }

    /**
     * Handle the request.
     *
     * @param  callable $handler
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(function (ResponseInterface $response) use ($options) {
                return $this->createResponseHandler($response, $options);
            });
        };
    }

    /**
     * Create response handler instance.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @param  array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function createResponseHandler(ResponseInterface $response, array $options): ResponseInterface
    {
        $handler = $this->getResponseHandler(
            $options['response_handler'] ?? $this->response
        );

        return $handler ? $handler::create($response) : $response;
    }

    /**
     * Get the response handler class name.
     *
     * @param  string $handler
     * @return string|null
     */
    protected function getResponseHandler($handler)
    {
        if ($handler && ! is_a($handler, ResponseHandlerInterface::class, true)) {
            throw new InvalidArgumentException(
                $handler.' must be an instance of '.ResponseHandlerInterface::class
            );
        }

        return $handler;
    }
}
