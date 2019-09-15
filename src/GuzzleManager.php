<?php

namespace Jenky\Guzzilla;

use Closure;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use Jenky\Guzzilla\Contracts\Guzzilla;

class GuzzleManager implements Guzzilla
{
    use Concerns\InteractsWithGuzzleConfiguration;

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
     * @return \Jenky\Guzzilla\Factory
     */
    protected function get($name)
    {
        return $this->channels[$name] ?? with($this->resolve($name), function ($client) use ($name) {
            return $this->channels[$name] = new Factory(
                $client, $this->configurationFor($name) ?: []
            );
        });
    }

    /**
     * Get the client configuration.
     *
     * @param  string  $name
     * @return array|null
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
        $options['handler'] = $options['handler'] ?? $this->createHandler($config);

        return new GuzzleClient($options);
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
