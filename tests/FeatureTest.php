<?php

namespace Jenky\Hermes\Test;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Event;
use Jenky\Hermes\Contracts\HttpResponseHandler;
use Jenky\Hermes\Events\RequestHandled;
use Jenky\Hermes\Response;
use Psr\Http\Message\RequestInterface;
use SimpleXMLElement;

class FeatureTest extends TestCase
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
