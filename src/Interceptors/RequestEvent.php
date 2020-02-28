<?php

namespace Jenky\Hermes\Interceptors;

use Jenky\Hermes\Middleware\RequestEvent as Middleware;

// @trigger_error(sprintf('The "%s" class is deprecated since Hermes 1.3.', RequestEvent::class), E_USER_DEPRECATED);

/**
 * @deprecated Use Jenky\Hermes\Middleware\RequestEvent instead. Will be removed in 2.0
 */
class RequestEvent extends Middleware
{
    //
}
