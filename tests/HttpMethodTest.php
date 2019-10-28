<?php

namespace Jenky\Hermes\Test;

class HttpMethodTest extends TestCase
{
    protected function assertTestCaseIsPassed($response)
    {
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function test_successful_get_request()
    {
        $this->assertTestCaseIsPassed(
            $this->httpClient()->get('get')
        );
    }

    public function test_post_request()
    {
        $this->assertTestCaseIsPassed(
            $this->httpClient()->post('post')
        );
    }

    public function test_put_request()
    {
        $this->assertTestCaseIsPassed(
            $this->httpClient()->put('put')
        );
    }

    public function test_patch_request()
    {
        $this->assertTestCaseIsPassed(
            $this->httpClient()->patch('patch')
        );
    }

    public function test_delete_request()
    {
        $this->assertTestCaseIsPassed(
            $this->httpClient()->delete('delete')
        );
    }
}
