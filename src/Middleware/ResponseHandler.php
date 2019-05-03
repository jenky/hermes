<?php

namespace Jenky\Guzzilla\Middleware;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;
use Jenky\Guzzilla\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseHandler
{
    /**
     * Handle the request.
     *
     * @param  callable $handler
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(function (ResponseInterface $response) {
                return Response::create($response);
            });
        };
    }
}
