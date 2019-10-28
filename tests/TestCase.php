<?php

namespace Jenky\Hermes\Test;

use Jenky\Hermes\Facades\Guzzle;
use Jenky\Hermes\HermesServiceProvider;
use Jenky\Hermes\Interceptors\RequestEvent;
use Jenky\Hermes\Interceptors\ResponseHandler;
use Jenky\Hermes\JsonResponse;
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
            HermesServiceProvider::class,
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

        $config->set('hermes.channels.httpbin', [
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
     * @return \Jenky\Hermes\GuzzleManager
     */
    protected function httpClient(array $options = [])
    {
        return Guzzle::channel('httpbin', $options);
    }
}
