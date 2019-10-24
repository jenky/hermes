<?php

namespace Jenky\Guzzilla\Contracts;

interface HttpResponseHandler extends ResponseHandler
{
    /**
     * Checks if HTTP Status code is Information (1xx).
     *
     * @return bool
     */
    public function isInformational(): bool;

    /**
     * Checks if HTTP Status code is a Redirect (3xx).
     *
     * @return bool
     */
    public function isRedirect(): bool;

    /**
     * Checks if HTTP Status code is a Client Error (4xx).
     *
     * @return bool
     */
    public function isClientError(): bool;

    /**
     * Checks if HTTP Status code is a Server Error (4xx).
     *
     * @return bool
     */
    public function isServerError(): bool;
}
