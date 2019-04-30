<?php

namespace Jenky\Guzzilla\Middleware;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Events\Dispatcher;
use Jenky\Guzzilla\Events\RequestHandled;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestEvent
{
    /**
     * The request instance.
     *
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * The response instance.
     *
     * @var \Psr\Http\Message\ResponseInterface|null
     */
    protected $response;

    /**
     * The request options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $dispatcher;

    /**
     * Create a new log writer instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher|null  $dispatcher
     * @return void
     */
    public function __construct(Dispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

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
            $this->request = $request;
            $this->response = $response;
            $this->options = $options;

            $this->fireEvent();

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
            $this->request = $request;
            $this->options = $options;

            if ($reason instanceof RequestException) {
                if ($reason->hasResponse()) {
                    $this->response = $reason->getResponse();
                }
            }

            $this->fireEvent();

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
     * @return void
     */
    protected function fireEvent()
    {
        if (isset($this->dispatcher)) {
            $this->dispatcher->dispatch(new RequestHandled(
                $this->request, $this->response, $this->options
            ));
        }
    }
}
