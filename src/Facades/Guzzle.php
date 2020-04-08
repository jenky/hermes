<?php

namespace Jenky\Hermes\Facades;

use Illuminate\Support\Facades\Facade;
use Jenky\Hermes\Contracts\Hermes;

/**
 * @method static \GuzzleHttp\Client channel(string $name, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface get(string|UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface head(string|UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface put(string|UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface post(string|UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface patch(string|UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface delete(string|UriInterface $uri, array $options = [])
 * @method static \GuzzleHttp\Promise\PromiseInterface getAsync(string|UriInterface $uri, array $options = [])
 * @method static \GuzzleHttp\Promise\PromiseInterface headAsync(string|UriInterface $uri, array $options = [])
 * @method static \GuzzleHttp\Promise\PromiseInterface putAsync(string|UriInterface $uri, array $options = [])
 * @method static \GuzzleHttp\Promise\PromiseInterface postAsync(string|UriInterface $uri, array $options = [])
 * @method static \GuzzleHttp\Promise\PromiseInterface patchAsync(string|UriInterface $uri, array $options = [])
 * @method static \GuzzleHttp\Promise\PromiseInterface deleteAsync(string|UriInterface $uri, array $options = [])
 *
 * @see \Jenky\Hermes\GuzzleManager
 * @see \GuzzleHttp\Client
 */
class Guzzle extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Hermes::class;
    }
}
