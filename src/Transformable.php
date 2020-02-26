<?php

namespace Jenky\Hermes;

interface Transformable
{
    /**
     * Transform the response body to native type.
     *
     * @return void
     */
    public function transform();
}
