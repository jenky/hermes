<?php

namespace Jenky\Hermes\Concerns;

use Illuminate\Support\Optional;

trait InteractsWithResponse
{
    /**
     * The response data.
     *
     * @var array
     */
    protected $data;

    /**
     * Get an attribute from the response data.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return data_get($this->data, $key, $default);
    }

    /**
     * Set an attribute to the response data.
     *
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    public function set($key, $value)
    {
        return data_set($this->data, $key, $value);
    }

    /**
     * Determine if the given key exists in the response body.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key): bool
    {
        $optional = new Optional($this->data);

        return isset($optional[$key]);
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->exists($key);
    }
}
