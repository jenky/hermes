<?php

namespace Jenky\Guzzilla;

use GuzzleHttp\HandlerStack;
use Illuminate\Support\Str;

trait InteractsWithGuzzleConfiguration
{
    /**
     * Create the client handler stack instance.
     *
     * @param  array $config
     * @return \GuzzleHttp\HandlerStack
     */
    public function createHandler(array $config = [])
    {
        return $this->prepareHandler(
            HandlerStack::create($this->handler($config)), $config
        );
    }

    /**
     * Get the handle stack's handler instance.
     *
     * @param  array $config
     * @return mixed
     */
    public function handler(array $config)
    {
        if (empty($config['request']['handler'])) {
            return;
        }

        $factory = is_callable($handler = $config['handler'])
            ? $handler
            : $this->app->make($handler, $config['handler_with'] ?? []);

        return $factory($config);
    }

    /**
     * Prepare handler stack for usage by Guzzle client.
     *
     * @param  \GuzzleHttp\HandlerStack $handler
     * @param  array $config
     * @return \GuzzleHttp\HandlerStack
     */
    protected function prepareHandler(HandlerStack $handler, array $config = [])
    {
        foreach ($this->middleware($config) as $name => $middleware) {
            $handler->push($middleware, $name);
        }

        return $this->tap($handler, $config);
    }

    /**
     * Apply the configured taps for the handle stack.
     *
     * @param  \GuzzleHttp\HandlerStack  $handler
     * @param  array  $config
     * @return \GuzzleHttp\HandlerStack
     */
    protected function tap(HandlerStack $handler, array $config = [])
    {
        foreach ($config['tap'] ?? [] as $tap) {
            [$class, $arguments] = $this->parseTap($tap);

            $this->app->make($class)->__invoke($handler, ...explode(',', $arguments));
        }

        return $handler;
    }

    /**
     * Parse the given tap class string into a class name and arguments string.
     *
     * @param  string  $tap
     * @return array
     */
    protected function parseTap($tap)
    {
        return Str::contains($tap, ':') ? explode(':', $tap, 2) : [$tap, ''];
    }

    /**
     * Get all middleware that will be pushed to handle stack instance.
     *
     * @param  array $config
     * @return array
     */
    public function middleware(array $config)
    {
        $middleware = [];

        foreach ($config['middleware'] ?? [] as $name => $parameters) {
            $middleware[$name] = $this->parseMiddleware($name, $parameters);
        }

        return $middleware;
    }

    /**
     * Parse the given middleware and create middleware instance with it's parameters.
     *
     * @param  string $name
     * @param  array $parameters
     * @return array
     */
    protected function parseMiddleware($name, $parameters)
    {
        if (is_callable($parameters)) {
            return $parameters;
        }

        return $this->app->make($name, $parameters);
    }
}
