<?php

namespace Jenky\Guzzilla\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface ResponseHandler extends Arrayable
{
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
