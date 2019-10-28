<?php

namespace Jenky\Hermes\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ResponseHandler extends ResponseInterface
{
    /**
     * Create new response handle instance.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseHandler
     */
    public static function create(ResponseInterface $response): ResponseInterface;

    /**
     * Determine that request is successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * Determine that request is error.
     *
     * @return bool
     */
    public function isError(): bool;
}
