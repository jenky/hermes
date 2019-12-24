<?php

namespace Jenky\Hermes;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Traits\ForwardsCalls;
use InvalidArgumentException;
use Jenky\Hermes\Contracts\Hermes;

class GuzzleManager implements Hermes
{
    use ForwardsCalls,
        Concerns\InteractsWithConfiguration;

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
     * Get all the channels.
     *
     * @return array
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * Get a client instance.
     *
     * @param  string  $channel
     * @param  array $options
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Client
     */
    public function channel($channel = null, array $options = [])
    {
        return $this->client(
            $channel ?: $this->getDefaultChannel(), $options
        );
    }

    /**
     * Attempt to get the client from the local cache.
     *
     * @param  string  $name
     * @param  array $options
     * @return \GuzzleHttp\Client
     */
    protected function client($name, array $options = [])
    {
        return $this->channels[$name] ?? with($this->resolve($name, $options), function ($client) use ($name) {
            return $this->channels[$name] = $client;
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
        return $this->app['config']["hermes.channels.{$name}"] ?? [];
    }

    /**
     * Resolve the given log instance by name.
     *
     * @param  string  $name
     * @param  array $options
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Client
     */
    protected function resolve($name, array $options = [])
    {
        $config = array_merge(
            $this->configurationFor($name), $options
        );

        if (empty($config)) {
            throw new InvalidArgumentException("Guzzle channel [{$name}] is not defined.");
        }

        if (empty($config['driver'])) {
            throw new InvalidArgumentException('Guzzle driver is not defined.');
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
     * Create a custom Guzzle driver instance.
     *
     * @param  array  $config
     * @return \GuzzleHttp\Client
     */
    protected function createCustomDriver(array $config)
    {
        $factory = is_callable($via = $config['via']) ? $via : $this->app->make($via);

        return $factory($config);
    }

    /**
     * Create a default Guzzle driver instance.
     *
     * @param  array  $config
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return \GuzzleHttp\Client
     */
    protected function createGuzzleDriver(array $config)
    {
        return new Client($this->makeClientOptions($config));
    }

    /**
     * Create a JSON Guzzle driver instance.
     *
     * @param  array  $config
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return \GuzzleHttp\Client
     */
    protected function createJsonDriver(array $config)
    {
        return new Client($this->makeClientOptions(
            array_merge_recursive($config, [
                'options' => [
                    'response_handler' => JsonResponse::class,
                ],
                'interceptors' => [
                    Interceptors\ResponseHandler::class,
                ],
            ])
        ));
    }

    /**
     * Get the default client name.
     *
     * @return string
     */
    public function getDefaultChannel()
    {
        return $this->app['config']['hermes.default'];
    }

    /**
     * Set the default guzzle client name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultChannel($name)
    {
        $this->app['config']['hermes.default'] = $name;
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
        return $this->forwardCallTo($this->channel(), $method, $parameters);
    }
}
