<?php

namespace Jenky\Hermes\Test;

use GuzzleHttp\Exception\GuzzleException;

class StatusCodeTest extends TestCase
{
    /* public function test_1xx_status_code()
    {
        $continue = $this->httpClient()->post('status/100');

        $this->assertEquals(100, $continue->getStatusCode());
        $this->assertTrue($continue->isInformational());
    } */

    public function test_2xx_status_code()
    {
        $ok = $this->httpClient()->get('status/200');
        $this->assertEquals(200, $ok->getStatusCode());
        $this->assertTrue($ok->ok());
        $this->assertTrue($ok->isSuccessful());

        $created = $this->httpClient()->get('status/201');
        $this->assertEquals(201, $created->getStatusCode());
        $this->assertTrue($created->created());
        $this->assertTrue($created->isSuccessful());
    }

    public function test_3xx_status_code()
    {
        $redirect = $this->httpClient()
            ->get('https://httpbin.org/redirect-to?url=https%3A%2F%2Fexample.com', [
                'allow_redirects' => false,
            ]);

        $this->assertEquals(302, $redirect->getStatusCode());
        $this->assertEquals(302, $redirect->status());
        $this->assertTrue($redirect->isRedirect());
    }

    public function test_4xx_status_code()
    {
        $client = $this->httpClient();

        $badRequest = $client->put('status/400');
        $this->assertEquals(400, $badRequest->getStatusCode());
        $this->assertTrue($badRequest->badRequest());
        $this->assertTrue($badRequest->isError());
        $this->assertTrue($badRequest->isClientError());

        $unauthorized = $client->put('status/401');
        $this->assertEquals(401, $unauthorized->getStatusCode());
        $this->assertTrue($unauthorized->unauthorized());
        $this->assertTrue($unauthorized->isError());
        $this->assertTrue($unauthorized->isClientError());

        $forbidden = $client->put('status/403');
        $this->assertEquals(403, $forbidden->getStatusCode());
        $this->assertTrue($forbidden->forbidden());
        $this->assertTrue($forbidden->isError());
        $this->assertTrue($forbidden->isClientError());

        $notFound = $client->put('status/404');
        $this->assertEquals(404, $notFound->getStatusCode());
        $this->assertTrue($notFound->notFound());
        $this->assertTrue($notFound->isError());
        $this->assertTrue($notFound->isClientError());

        $unprocessable = $client->get('https://httpbin.org/status/422');
        $this->assertEquals(422, $unprocessable->getStatusCode());
        $this->assertTrue($unprocessable->unprocessable());
        $this->assertTrue($unprocessable->isError());
        $this->assertTrue($unprocessable->isClientError());
    }

    public function test_5xx_status_code()
    {
        $client = $this->httpClient();

        $internalError = $client->patch('status/500');
        $this->assertEquals(500, $internalError->getStatusCode());
        $this->assertTrue($internalError->isError());
        $this->assertTrue($internalError->serverError());
        $this->assertTrue($internalError->isServerError());

        $timeout = $client->patch('status/503');
        $this->assertEquals(503, $timeout->getStatusCode());
        $this->assertTrue($timeout->isError());
        $this->assertTrue($timeout->isServerError());
    }
}
