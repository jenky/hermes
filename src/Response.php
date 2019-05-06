<?php

namespace Jenky\Guzzilla;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Traits\Macroable;
use Jenky\Guzzilla\Contracts\HttpResponseHanlder;
use Psr\Http\Message\ResponseInterface;

class Response extends GuzzleResponse implements HttpResponseHanlder
{
    use Macroable;

    /**
     * Create new response handle instance.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function create(ResponseInterface $response): ResponseInterface
    {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    /**
     * Determine that request is successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        $statusCode = $this->getStatusCode();

        return ($statusCode >= 200 && $statusCode < 300) || $statusCode == 304;
    }

    /**
     * Determine that request is error.
     *
     * @return bool
     */
    public function isError()
    {
        return ! $this->isSuccessful();
    }

    /**
     * Checks if HTTP Status code is Information (1xx).
     *
     * @return bool
     */
    public function isInformational()
    {
        return $this->getStatusCode() < 200;
    }

    /**
     * Checks if HTTP Status code is a Redirect (3xx).
     *
     * @return bool
     */
    public function isRedirect()
    {
        $statusCode = $this->getStatusCode();

        return $statusCode >= 300 && $statusCode < 400;
    }

    /**
     * Checks if HTTP Status code is a Client Error (4xx).
     *
     * @return bool
     */
    public function isClientError()
    {
        $statusCode = $this->getStatusCode();

        return $statusCode >= 400 && $statusCode < 500;
    }

    /**
     * Checks if HTTP Status code is a Server Error (4xx).
     *
     * @return bool
     */
    public function isServerError()
    {
        $statusCode = $this->getStatusCode();

        return $statusCode >= 500;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [];
    }
}
