<?php

namespace Jenky\Hermes;

interface Parsable
{
    /**
     * Parse the response body to native type.
     *
     * @return void
     */
    public function parse();
}
