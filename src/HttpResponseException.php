<?php

namespace Jenky\Hermes;

use Jenky\Hermes\Contracts\ResponseException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpResponseException extends HttpException
{
    /**
     * The response instance.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    public $response;

    /**
     * Create new HTTP exception instance from PSR-7 HTTP response message.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @param  \Throwable|null $previous
     * @return self
     */
    public static function fromResponse(ResponseInterface $response, ?\Throwable $previous = null): self
    {
        $headers = array_map(function ($values) {
            return $values[0] ?? null;
        }, $response->getHeaders());

        if ($response instanceof ResponseException) {
            $message = $response->message();
            $code = $response->code();
        } else {
            $message = static::message($response);
            $code = static::code($response);
        }

        return tap(new static(
            $response->getStatusCode(),
            $message ?: $response->getReasonPhrase(),
            $previous,
            array_filter($headers),
            $code
        ), function ($e) use ($response) {
            $e->response = $response;

            return $e;
        });
    }

    /**
     * Get the response exception message.
     *
     * @return string|null
     */
    protected static function message(ResponseInterface $response): ?string
    {
        return $response->message;
    }

    /**
     * Get the response exception code.
     *
     * @return int|null
     */
    protected static function code(ResponseInterface $response): ?int
    {
        return intval($response->code);
    }
}
