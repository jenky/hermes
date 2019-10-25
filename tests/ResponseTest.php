<?php

namespace Jenky\Guzzilla\Test;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\JsonResponse;
use Jenky\Guzzilla\Response;

class ResponseTest extends TestCase
{
    public function test_json_response()
    {
        $response = $this->httpClient()->get('json');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->header('content-type'));
        $this->assertJson($response->toJson());

        TestResponse::fromBaseResponse(
            new JsonResponse($response->toArray(), $response->getStatusCode(), $response->header())
        )
            ->assertHeader('content-type', 'application/json')
            ->assertSuccessful()
            ->assertJsonStructure([
                'slideshow' => [
                    'author',
                    'date',
                    'slides',
                    'title',
                ]
            ]);
    }

    public function test_xml_response()
    {
        $response = $this->httpClient()->get('xml', [
            'response_handler' => Response::class,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/xml', $response->header('content-type'));
    }
}
