<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Guzzle Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default guzzle channel that gets used when
    | sending requests to the services. The name specified in this option
    | should match one of the channels defined in the "channels"
    | configuration array.
    |
    */

    'default' => env('HERMES_CHANNEL', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Guzzle Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the guzzle channels for your application.
    | In addition, you may set any custom options as needed by the particular
    | channel you choose.
    |
    */

    'channels' => [
        'default' => [
            'driver' => 'guzzle',
            'options' => [
                //
            ],
            'middleware' => [
                Jenky\Hermes\Middleware\RequestEvent::class,
            ],
        ],
    ],

];
