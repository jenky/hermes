<?php

namespace Jenky\Hermes;

class LazyEvaluation
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * Create a new lazy evaluation instance.
     *
     * @param  callable $callable
     * @return void
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * Return the callable.
     *
     * @return callable
     */
    public function __invoke()
    {
        return $this->callable->__invoke();
    }
}
