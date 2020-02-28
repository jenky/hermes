<?php

namespace Jenky\Hermes\Interceptors;

use Jenky\Hermes\Middleware\ResponseHandler as Middleware;

// @trigger_error(sprintf('The "%s" class is deprecated since Hermes 1.3.', ResponseHandler::class), E_USER_DEPRECATED);

/**
 * @deprecated Use Jenky\Hermes\Middleware\ResponseHandler instead. Will be removed in 2.0
 */
class ResponseHandler extends Middleware
{
    //
}
