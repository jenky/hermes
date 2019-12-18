<?php

namespace Jenky\Hermes\Interceptors;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Events\Dispatcher;
use Jenky\Hermes\Events\RequestHandled;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestEvent
{
    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $dispatcher;

    /**
     * Create a new handler instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher|null  $dispatcher
     * @return void
     */
    public function __construct(Dispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle the request.
     *
     * @param  callable $handler
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(
                $this->handleSuccess($request, $options),
                $this->handleFailure($request, $options)
            );
        };
    }

    /**
     * Handler on fulfilled request.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function handleSuccess(RequestInterface $request, array $options)
    {
        return function (ResponseInterface $response) use ($request, $options) {
            $this->fireEvent($request, $response, $options);

            return $response;
        };
    }

    /**
     * Handler on rejected request.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  array $options
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function handleFailure(RequestInterface $request, array $options)
    {
        return function ($reason) use ($request, $options) {
            $response = null;

            if ($reason instanceof RequestException) {
                if ($reason->hasResponse()) {
                    $response = $reason->getResponse();
                }
            }

            $this->fireEvent($request, $response, $options);

            return \GuzzleHttp\Promise\rejection_for($reason);
        };
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @return void
     */
    public function setEventDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Fires a request event.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  \Psr\Http\Message\ResponseInterface|null $response
     * @param  array $options
     * @return void
     */
    protected function fireEvent(RequestInterface $request, ResponseInterface $response = null, array $options = [])
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(new RequestHandled(
                $request, $response, $options
            ));
        }
    }
}
