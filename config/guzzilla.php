<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Guzzle Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default guzzle channel that gets used when sending
    | requests to the services. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('GUZZILLA_CHANNEL', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Guzzle Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the guzzle channels for your application.
    |
    */

    'channels' => [
        'default' => [
            'driver' => 'guzzle',
            'options' => [
                //
            ],
            'tap' => [
                //
            ],
            'handler' => null,
            'with' => [
                //
            ],
            'middleware' => [
                \Jenky\Guzzilla\Middleware\RequestEvent::class,
                \Jenky\Guzzilla\Middleware\ResponseHandler::class => [
                    'response' => \Jenky\Guzzilla\Response::class
                ],
            ],
        ],
    ],

];
