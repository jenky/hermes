<?php

namespace Jenky\Hermes\Facades;

use Illuminate\Support\Facades\Facade;
use Jenky\Hermes\Contracts\Hermes;
use Jenky\Hermes\Testing\Fakes\GuzzleFake;

class Guzzle extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return \Illuminate\Support\Testing\Fakes\EventFake
     */
    public static function fake()
    {
        static::swap($fake = new GuzzleFake(
            static::getFacadeApplication()
        ));

        return $fake;
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Hermes::class;
    }
}
