<?php

namespace Jenky\Guzzilla\Test;

use GuzzleHttp\Exception\GuzzleException;

class StatusCodeTest extends TestCase
{
    public function test_1xx_status_code()
    {
        $continue = $this->httpClient()->post('status/100');

        $this->assertEquals(100, $continue->getStatusCode());
        $this->assertTrue($continue->isInformational());
    }

    public function test_2xx_status_code()
    {
        $ok = $this->httpClient()->get('status/200');
        $this->assertEquals(200, $ok->getStatusCode());
        $this->assertTrue($ok->isSuccessful());

        $created = $this->httpClient()->get('status/201');
        $this->assertEquals(201, $created->getStatusCode());
        $this->assertTrue($created->isSuccessful());
    }

    public function test_3xx_status_code()
    {
        $redirect = $this->httpClient()->post('status/301');

        $this->assertEquals(200, $redirect->getStatusCode());
        $this->assertTrue($redirect->isRedirect());
    }

    public function test_4xx_status_code()
    {
        $client = $this->httpClient();

        $badRequest = $client->put('status/400');
        $this->assertEquals(400, $badRequest->getStatusCode());

        $unauthorized = $client->put('status/401');
        $this->assertEquals(401, $unauthorized->getStatusCode());

        $forbidden = $client->put('status/403');
        $this->assertEquals(403, $forbidden->getStatusCode());

        $notfound = $client->put('status/404');
        $this->assertEquals(404, $notfound->getStatusCode());

        $this->expectException(GuzzleException::class);

        $unprocessable = guzzle()->get('https://httpbin.org/status/422');
        $this->assertEquals(422, $unprocessable->getStatusCode());
        $this->assertTrue($unprocessable->isError());
        $this->assertTrue($unprocessable->isClientError());
    }

    public function test_5xx_status_code()
    {
        $client = $this->httpClient();

        $internalError = $client->patch('status/500');
        $this->assertEquals(500, $internalError->getStatusCode());
        $this->assertTrue($internalError->isError());
        $this->assertTrue($internalError->isServerError());

        $timeout = $client->patch('status/503');
        $this->assertEquals(503, $timeout->getStatusCode());
        $this->assertTrue($timeout->isError());
        $this->assertTrue($timeout->isServerError());
    }
}
