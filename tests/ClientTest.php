<?php

namespace Jenky\Guzzilla\Test;

use GuzzleHttp\Client;

class ClientTest extends TestCase
{
    public function test_client_is_instance_of_guzzle()
    {
        $this->assertInstanceOf(Client::class, guzzle());
    }
}
