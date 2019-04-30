<?php

namespace Jenky\Guzzilla;

use Closure;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Jenky\Guzzilla\Contracts\Guzzilla;

class GuzzleManager implements Guzzilla
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved channels.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new Guzzle manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get a client instance.
     *
     * @param  string  $channel
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Client
     */
    public function channel($channel = null)
    {
        return $this->get($channel ?: $this->getDefaultChannel());
    }

    /**
     * Attempt to get the log from the local cache.
     *
     * @param  string  $name
     * @return \GuzzleHttp\ClientInterface
     */
    protected function get($name)
    {
        return $this->channels[$name] ?? with($this->resolve($name), function ($client) use ($name) {
            // return $this->channels[$name] = $this->tap($name, new Client($client, $this->app['events']));
            return $this->channels[$name] = new Client($client, $this->app['events']);
        });
    }

    /**
     * Get the client configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function configurationFor($name)
    {
        return $this->app['config']["guzzilla.channels.{$name}"];
    }

    /**
     * Resolve the given log instance by name.
     *
     * @param  string  $name
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\ClientInterface
     */
    protected function resolve($name)
    {
        $config = $this->configurationFor($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Guzzle driver [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string    $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Create a custom log driver instance.
     *
     * @param  array  $config
     * @return \GuzzleHttp\ClientInterface
     */
    protected function createCustomDriver(array $config)
    {
        $factory = is_callable($via = $config['via']) ? $via : $this->app->make($via);

        return $factory($config);
    }

    /**
     * Create an instance of any handler available in Monolog.
     *
     * @param  array  $config
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return \GuzzleHttp\ClientInterface
     */
    protected function createGuzzleDriver(array $config)
    {
        $options = $config['options'] ?? [];
        $options['handler'] = $this->createHandler($config);

        return new GuzzleClient($options);
    }

    /**
     * Get the client handler stack instance.
     *
     * @param  array $config
     * @return \GuzzleHttp\HandlerStack
     */
    protected function createHandler(array $config = [])
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
    protected function handler(array $config)
    {
        if (empty($config['handler'])) {
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
        return $this->tap(
            $this->prepareMiddleware($handler, $config['middleware'] ?? []),
            $config
        );
    }

    /**
     * Apply the configured taps for the client.
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

    protected function prepareMiddleware(HandlerStack $stack, array $middleware = [])
    {
        foreach ($middleware as $name => $options) {
            $stack->push(
                $this->parseMiddleware($name, $options), $name
            );
        }

        return $stack;
    }

    protected function parseMiddleware($name, $options)
    {
        if (is_callable($options)) {
            return $options;
        }

        return $this->app->make($name, $options);
    }

    /**
     * Get the default client name.
     *
     * @return string
     */
    public function getDefaultChannel()
    {
        return $this->app['config']['guzzilla.default'];
    }

    /**
     * Set the default guzzle client name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultChannel($name)
    {
        $this->app['config']['guzzilla.default'] = $name;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->channel()->{$method}(...$parameters);
    }
}
