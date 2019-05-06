<?php

namespace Jenky\Guzzilla\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Psr\Http\Message\ResponseInterface;

interface ResponseHandler extends Arrayable
{
    /**
     * Create new response handle instance.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function create(ResponseInterface $response): ResponseInterface;

    /**
     * Determine that request is successful.
     *
     * @return bool
     */
    public function isSuccessful();

    /**
     * Determine that request is error.
     *
     * @return bool
     */
    public function isError();
}
