<?php

namespace Jenky\Guzzilla\Test;

use Jenky\Guzzilla\Contracts\ResponseHandler;

class HttpMethodTest extends TestCase
{
    protected function assertTestCaseIsPassed(ResponseHandler $response)
    {
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function test_successful_get_request()
    {
        $this->assertTestCaseIsPassed(
            guzzle()->get('httpbin.org/get')
        );
    }

    public function test_post_request()
    {
        $this->assertTestCaseIsPassed(
            guzzle()->post('httpbin.org/post')
        );
    }

    public function test_put_request()
    {
        $this->assertTestCaseIsPassed(
            guzzle()->put('httpbin.org/put')
        );
    }

    public function test_patch_request()
    {
        $this->assertTestCaseIsPassed(
            guzzle()->patch('httpbin.org/patch')
        );
    }

    public function test_delete_request()
    {
        $this->assertTestCaseIsPassed(
            guzzle()->delete('httpbin.org/delete')
        );
    }
}
