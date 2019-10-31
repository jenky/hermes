<?php

namespace Jenky\Hermes\Test;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Event;
use Jenky\Hermes\Events\RequestHandled;
use Psr\Http\Message\RequestInterface;

class FunctionalTest extends TestCase
{
    public function test_client_is_instance_of_guzzle()
    {
        $this->assertInstanceOf(Client::class, guzzle());
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
        ])->get('https://httpbin.org/headers');

        $this->assertEquals('bar', $response->get('headers.X-Foo'));
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
