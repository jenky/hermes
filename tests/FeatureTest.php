<?php

namespace Jenky\Hermes\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Jenky\Hermes\Contracts\Hermes;
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

        $this->app[Hermes::class]->extend('rss', function ($app, array $config) {
            return new Client($this->makeClientOptions(
                \Jenky\Hermes\array_merge_recursive_distinct($config, [
                    'options' => [
                        'response_handler' => XmlResponse::class,
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

        $app['config']->set('hermes.channels.reqres', [
            'driver' => 'json',
            'options' => [
                'base_uri' => 'https://reqres.in',
                'response_handler' => ReqresResponse::class,
            ],
        ]);

        $app['config']->set('hermes.channels.googlenews', [
            'driver' => 'rss',
            'options' => [
                'base_uri' => 'https://news.google.com',
                'http_errors' => false,
            ],
        ]);

        $app['config']->set('hermes.channels.custom', [
            'driver' => 'custom',
            'via' => CreateCustomDriver::class,
        ]);

        $app['config']->set('hermes.channels.lazy', [
            'driver' => 'json',
            'options' => [
                'base_uri' => 'https://httpbin.org',
            ],
            'interceptors' => [
                \Jenky\Hermes\lazy(function () {
                    return Middleware::log(logs(), new MessageFormatter);
                }),
            ],
        ]);
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

    public function test_exception()
    {
        $this->expectException(GuzzleException::class);

        $this->httpClient()->get('https://httpbin.org/status/422', [
            'http_errors' => true,
        ]);
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
        $response = guzzle('googlenews')->get('news/rss');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->body());
        $this->assertInstanceOf(SimpleXMLElement::class, $response->toXml());
    }

    public function test_json_driver()
    {
        $response = guzzle('reqres')->get('api/users');

        $this->assertInstanceOf(ReqresResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function test_custom_driver()
    {
        $response = guzzle('custom')->get('https://example.com');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_driver_not_supported()
    {
        $this->expectException(\InvalidArgumentException::class);

        guzzle('foo')->get('https://example.com');
    }

    public function test_mutable_client()
    {
        $response = $this->httpClient()->get('bearer');

        $this->assertTrue($response->isClientError());
        $this->assertEquals(401, $response->getStatusCode());

        // Mutate the client by creating new client instance
        $apiKey = (string) Str::uuid();

        $this->httpClient([
            'options' => [
                'headers' => [
                    'Authorization' => 'Bearer '.$token = Str::random(),
                ],
            ],
            'middleware' => [
                function (callable $handler) use ($apiKey) {
                    return function (RequestInterface $request, array $options) use ($handler, $apiKey) {
                        $request = $request->withHeader('X-Api-Key', $apiKey);

                        return $handler($request, $options);
                    };
                },
                Middleware::mapRequest(function (RequestInterface $request) {
                    return $request->withHeader('Foo', 'Bar');
                }),
            ],
        ]);

        $response = $this->httpClient()->get('bearer');

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->authenticated);
        $this->assertEquals($token, $response->token);

        $response = $this->httpClient()->get('anything');

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->get('headers.X-Api-Key'), $apiKey);
        $this->assertEquals($response->get('headers.Foo'), 'Bar');
    }

    public function test_default_channel()
    {
        guzzle()->setDefaultChannel('jsonplaceholder');

        $response = guzzle()->get('users');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string) $response->getBody());
        $this->assertNotEmpty($response->toArray());
    }

    public function test_lazy_evaluate_middleware()
    {
        Event::fake();

        $response = guzzle('lazy')->get('uuid');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($response->uuid);

        Event::assertDispatched(MessageLogged::class);
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

class XmlResponse extends Response
{
    public function toXml()
    {
        return new SimpleXMLElement((string) $this->getBody());
    }
}

class ReqresResponse extends JsonResponse
{
    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && ! empty($this->get('data'));
    }
}
