<?php

namespace Jenky\Hermes\Contracts;

interface ResponseException
{
    /**
     * Get the response exception message.
     *
     * @return string
     */
    public function message(): string;

    /**
     * Get the response exception code.
     *
     * @return int
     */
    public function code(): int;
}
