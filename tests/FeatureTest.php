<?php

namespace Jenky\Hermes\Test;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Event;
use Jenky\Hermes\Contracts\Hermes;
use Jenky\Hermes\Contracts\HttpResponseHandler;
use Jenky\Hermes\Events\RequestHandled;
use Jenky\Hermes\Interceptors\ResponseHandler;
use Jenky\Hermes\JsonResponse;
use Jenky\Hermes\Response;
use Psr\Http\Message\RequestInterface;
use SimpleXMLElement;

class FeatureTest extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app[Hermes::class]->extend('json', function ($app, array $config) {
            return new Client($this->makeClientOptions(
                array_merge_recursive($config, [
                    'options' => [
                        'response_handler' => JsonResponse::class,
                    ],
                    'interceptors' => [
                        ResponseHandler::class,
                    ],
                ]))
            );
        });
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('hermes.channels.jsonplaceholder', [
            'driver' => 'json',
            'options' => [
                'base_uri' => 'https://jsonplaceholder.typicode.com',
                'http_errors' => false,
            ],
        ]);

        $app['config']->set('hermes.channels.custom', [
            'driver' => 'custom',
            'via' => CreateCustomDriver::class,
        ]);
    }

    public function test_client_is_instance_of_guzzle()
    {
        $this->assertInstanceOf(Client::class, guzzle()->channel());
    }

    public function test_request_event_fired()
    {
        Event::fake();

        $this->httpClient()->get('https://example.com');

        Event::assertDispatched(RequestHandled::class);
    }

    public function test_tap()
    {
        $response = $this->httpClient([
            'tap' => [
                AddHeaderToRequest::class.':X-Foo,bar',
            ],
        ])->get('headers');

        $this->assertEquals('bar', $response->get('headers.X-Foo'));
    }

    public function test_response_handler()
    {
        $response = $this->httpClient()->get('xml', [
            'response_handler' => XmlResponse::class,
        ]);

        $this->assertInstanceOf(SimpleXMLElement::class, $response->toXml());

        $this->expectException(\InvalidArgumentException::class);

        $this->httpClient()->get('html', [
            'response_handler' => InvalidResponseHandler::class,
        ]);
    }

    public function test_driver()
    {
        $response = guzzle('jsonplaceholder')->get('users');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string) $response->getBody());
        $this->assertNotEmpty($response->toArray());
    }

    public function test_custom_driver()
    {
        $response = guzzle('custom')->get('https://example.com');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_default_channel()
    {
        guzzle()->setDefaultChannel('jsonplaceholder');

        $response = guzzle()->get('users');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string) $response->getBody());
        $this->assertNotEmpty($response->toArray());
    }
}

class CreateCustomDriver
{
    public function __invoke(array $config)
    {
        return new Client($config['options'] ?? []);
    }
}

class AddHeaderToRequest
{
    public function __invoke(HandlerStack $handler, $header, $value)
    {
        $handler->push(Middleware::mapRequest(function (RequestInterface $request) use ($header, $value) {
            return $request->withHeader($header, $value);
        }));
    }
}

class InvalidResponseHandler
{
}

class XmlResponse extends Response implements HttpResponseHandler
{
    public function toXml()
    {
        return new SimpleXMLElement((string) $this->getBody());
    }
}
