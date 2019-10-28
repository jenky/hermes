<?php

namespace Jenky\Hermes\Test;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Event;
use Jenky\Hermes\Events\RequestHandled;

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
}
