<?php

namespace Jenky\Hermes;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class JsonResponse extends Response implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    use Concerns\InteractsWithResponse;

    /**
     * The JSON data.
     *
     * @var array
     */
    protected $data;

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        if (is_null($this->data)) {
            $this->data = json_decode(
                $this->getBody()->__toString(), true
            ) ?: [];
        }

        return $this->data;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the fluent instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
