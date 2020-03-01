<?php

namespace Jenky\Hermes\Concerns;

trait InteractsWithMessage
{
    /**
     * Retrieve a header from the request.
     *
     * @param  string|null  $key
     * @param  string|array|null  $default
     * @return string|array|null
     */
    public function header($header = null, $default = null)
    {
        if ($header) {
            return $this->getHeader($header)[0] ?? $default;
        }

        return array_map(function ($values) {
            return $values[0] ?? null;
        }, $this->getHeaders());
    }

    /**
     * Get or check the status code.
     *
     * @param  int|null $code
     * @return int|bool
     */
    public function status($code = null)
    {
        $statusCode = $this->getStatusCode();

        return $code ? $statusCode == $code : $statusCode;
    }

    /**
     * Get the response body as string.
     *
     * @return string
     */
    public function body()
    {
        return (string) $this->getBody();
    }

    /**
     * Determine that response status code is ok.
     *
     * @return bool
     */
    public function ok(): bool
    {
        return $this->status(200);
    }

    /**
     * Determine that response status code is created.
     *
     * @return bool
     */
    public function created(): bool
    {
        return $this->status(201);
    }

    /**
     * Determine that response status code is bad request.
     *
     * @return bool
     */
    public function badRequest(): bool
    {
        return $this->status(400);
    }

    /**
     * Determine that response status code is unauthorized.
     *
     * @return bool
     */
    public function unauthorized(): bool
    {
        return $this->status(401);
    }

    /**
     * Determine that response status code is forbidden.
     *
     * @return bool
     */
    public function forbidden(): bool
    {
        return $this->status(403);
    }

    /**
     * Determine that response status code is not found.
     *
     * @return bool
     */
    public function notFound(): bool
    {
        return $this->status(404);
    }

    /**
     * Determine that response status code is unprocessable.
     *
     * @return bool
     */
    public function unprocessable(): bool
    {
        return $this->status(422);
    }

    /**
     * Determine that response status code is internal server error.
     *
     * @return bool
     */
    public function serverError(): bool
    {
        return $this->status(500);
    }
}
