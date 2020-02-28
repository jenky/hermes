# Hermes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)

The package provides a nice and easy wrapper around Guzzle for use in your Laravel applications. If you don't know what Guzzle does, [take a peek at their intro](http://docs.guzzlephp.org/en/stable/index.html). Shortly said, Guzzle is a PHP HTTP client that makes it easy to send HTTP requests and trivial to integrate with web service.

- [Hermes](#hermes)
  - [Install](#install)
  - [Configuration](#configuration)
    - [Channel configuration](#channel-configuration)
    - [Configure the guzzle option](#configure-the-guzzle-option)
    - [Configure the guzzle handler](#configure-the-guzzle-handler)
    - [Configure the guzzle middleware / interceptors](#configure-the-guzzle-middleware--interceptors)
    - [Customizing the guzzle handler stack](#customizing-the-guzzle-handler-stack)
  - [Middleware](#middleware)
    - [`RequestEvent`](#requestevent)
    - [`ResponseHandler`](#responsehandler)
  - [Usage](#usage)
  - [Change log](#change-log)
  - [Testing](#testing)
  - [Contributing](#contributing)
  - [Security](#security)
  - [Credits](#credits)
  - [License](#license)

## Install

You may use Composer to install Hermes into your Laravel project:

``` bash
$ composer require jenky/hermes
```

After installing Hermes, publish its assets using the `vendor:publish` Artisan command.

``` bash
php artisan vendor:publish
```

or

``` bash
php artisan vendor:publish --provider="Jenky\Hermes\HermesServiceProvider"
```

## Configuration

After publishing Hermes's assets, its primary configuration file will be located at `config/hermes.php`. This configuration file allows you to configure your guzzle client options and each configuration option includes a description of its purpose, so be sure to thoroughly explore this file.

### Channel configuration

A channel is simply a guzzle http client instance with its own configuration. This allows you to create a http client on the fly and reuse anytime, anywhere you want.

### Configure the guzzle option

Set guzzle request options within the channel. Please visit [Request Options](http://docs.guzzlephp.org/en/stable/request-options.html) for more information.

``` php
'default' => [
    'options' => [
        'base_uri' => 'https://api.github.com/v3/',
        'time_out' => 20,
    ],
],
```

### Configure the guzzle handler
Configure guzzle [Handler](http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#handlers) within the channel.

By default, guzzle will choose the most appropriate handler based on the extensions available on your system. However you can override this behavior with `handler` option. Optionally, any constructor parameters the handler needs may be specified using the `with` configuration option:

``` php
'default' => [
    'handler' => App\Http\CustomCurlHandler::class,
    'with' => [
        'delay' => 5,
    ],
],
```

An alternative way is set the handler in the [`options`](#configure-the-guzzle-option) configuration:

``` php
'default' => [
    'options' => [
        'handler' => App\Http\CustomCurlHandler::create(['delay' => 5]),
    ],
],
```

### Configure the guzzle middleware / interceptors

Configure guzzle [Middleware](http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#middleware) within the channel.

``` php
'default' => [
    'interceptors' => [
        Jenky\Hermes\Interceptor\RequestEvent::class,
    ],
],
```

> The package ships with 2 interceptors. You can read about the interceptors in the [middleware](#middleware) section.

**Lazy evaluation**

If your middleware use Laravel service container binding implementations such as config, session driver, logger inside the `hermes` config file, you'll need to create your middleware using `Jenky\Hermes\lazy()` function. This is because those implementations are not yet bound to the container when the `hermes` config is loaded. The `lazy` function will wrap your middleware inside a `Closure` then invokes when parsing the configuration.

``` php
'interceptors' => [
    // This won't work
    GuzzleHttp\Middleware::log(logs(), new GuzzleHttp\MessageFormatter),

    // This should work
    Jenky\Hermes\lazy(function () {
        return GuzzleHttp\Middleware::log(logs(), new GuzzleHttp\MessageFormatter);
    }),
],
```

### Customizing the guzzle handler stack

Sometimes you may need complete control over how guzzle's [HandleStack](http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#handlerstack) is configured for an existing channel. For example, you may want to add, remove or unshift a middleware for a given channel's handler stack.

To get started, define a `tap` array on the channel's configuration. The `tap` array should contain a list of classes that should have an opportunity to customize (or "tap" into) the handle stack instance after it is created:

``` php
'default' => [
    'tap' => [
        App\Http\CustomizeHandlerStack::class,
    ],
],
```

Once you have configured the `tap` option on your channel, you're ready to define the class that will customize your `HandlerStack` instance. This class only needs a single method: `__invoke`, which receives an `GuzzleHttp\HandlerStack` instance.

``` php
<?php

namespace App\Http;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class CustomizeHandlerStack
{
    /**
     * Customize the given handler stack instance.
     *
     * @param  \GuzzleHttp\HandlerStack  $stack
     * @return void
     */
    public function __invoke(HandlerStack $stack)
    {
        $stack->before('add_foo', Middleware::mapRequest(function (RequestInterface $request) {
            return $request->withHeader('X-Baz', 'Qux');
        }, 'add_baz');
    }
}
```

> All of your "tap" classes are resolved by the service container, so any constructor dependencies they require will automatically be injected.

## Middleware

### `RequestEvent`

This middleware will fire `Jenky\Hermes\Events\RequestHandled` event when a request had been fulfilled. It has these properties:

``` php
/**
 * The request instance.
 *
 * @var \Psr\Http\Message\RequestInterface
 */
public $request;

/**
 * The response instance.
 *
 * @var \Psr\Http\Message\ResponseInterface|null
 */
public $response;

/**
 * The request options.
 *
 * @var array
 */
public $options;
```

### `ResponseHandler`

When sending the request, `GuzzleHttp\Psr7\Response` will be used as the default response handler. However you can configure the request options to use your own response handler.

``` php
'default' => [
    'options' => [
        'base_uri' => 'https://httpbin.org/',
        // ...
        'response_handler' => Jenky\Hermes\JsonResponse::class,
    ],
    'interceptors' => [
        Jenky\Hermes\Interceptors\ResponseHandler::class,
        // ...
    ],
],
```

## Usage

``` php
use Jenky\Hermes\Facades\Guzzle;

Guzzle::get('https://jsonplaceholder.typicode.com/users');
// or using helper
guzzle()->get('https://jsonplaceholder.typicode.com/users');
```

Sometimes you may wish to send a request to a channel other than your application's default channel. You may use the `channel` method on the `Guzzle` facade to retrieve and send to any channel defined in your configuration file:

``` php
use Jenky\Hermes\Facades\Guzzle;

Guzzle::channel('my_channel')->get('https://jsonplaceholder.typicode.com/users');
// or using helper
guzzle('my_channel')->get('https://jsonplaceholder.typicode.com/users');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email contact@lynh.me instead of using the issue tracker.

## Credits

- [Lynh][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jenky/hermes.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/com/jenky/hermes/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/jenky/hermes.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/jenky/hermes.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/jenky/hermes.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jenky/hermes
[link-travis]: https://travis-ci.com/jenky/hermes
[link-scrutinizer]: https://scrutinizer-ci.com/g/jenky/hermes/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/jenky/hermes
[link-downloads]: https://packagist.org/packages/jenky/hermes
[link-author]: https://github.com/jenky
[link-contributors]: ../../contributors
