<?php

namespace Jenky\Hermes\Test;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\JsonResponse;
use Jenky\Hermes\Response;

class ResponseTest extends TestCase
{
    public function test_json_response()
    {
        $response = $this->httpClient()->get('json');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->header('Content-Type'));
        $this->assertJson($response->toJson());
        $this->assertEqualsIgnoringCase('Sample Slide Show', $response->get('slideshow.title'));
        $this->assertEqualsIgnoringCase('Sample Slide Show', $response->slideshow['title']);
        $response->test = true;
        $this->assertTrue($response->test);

        TestResponse::fromBaseResponse(
            new JsonResponse($response->toArray(), $response->getStatusCode(), $response->header())
        )
            ->assertHeader('Content-Type', 'application/json')
            ->assertSuccessful()
            ->assertJsonStructure([
                'test',
                'slideshow' => [
                    'author',
                    'date',
                    'slides',
                    'title',
                ],
            ]);

        unset($response->test);
        $this->assertFalse(isset($response->test));
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
