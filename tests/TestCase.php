<?php

namespace Jenky\Guzzilla\Test;

use Jenky\Guzzilla\Facades\Guzzle;
use Jenky\Guzzilla\GuzzillaServiceProvider;
use Jenky\Guzzilla\Interceptors\RequestEvent;
use Jenky\Guzzilla\Interceptors\ResponseHandler;
use Jenky\Guzzilla\JsonResponse;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            GuzzillaServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->get('config');

        $config->set('database.default', 'testbench');

        $config->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $config->set('guzzilla.channels.httpbin', [
            'driver' => 'guzzle',
            'options' => [
                'base_uri' => 'https://httpbin.org',
                'http_errors' => false,
                'response_handler' => JsonResponse::class,
            ],
            'tap' => [
                //
            ],
            'handler' => null,
            'with' => [
                //
            ],
            'interceptors' => [
                RequestEvent::class,
                ResponseHandler::class,
            ],
        ]);
    }

    /**
     * Get the httpbin client.
     *
     * @param  array $options
     * @return \Jenky\Guzzilla\GuzzleManager
     */
    protected function httpClient(array $options = [])
    {
        return Guzzle::channel('httpbin', $options);
    }
}
