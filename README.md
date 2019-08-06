# Guzzilla

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

The package provides a nice and easy wrapper around Guzzle for use in your Laravel applications. If you don't know what Guzzle does, [take a peek at their intro](http://docs.guzzlephp.org/en/stable/index.html). Shortly said, Guzzle is a PHP HTTP client that makes it easy to send HTTP requests and trivial to integrate with web service.

## Install

You may use Composer to install Guzzilla into your Laravel project:

``` bash
$ composer require jenky/guzzilla
```

After installing Guzzilla, publish its assets using the `vendor:publish` Artisan command.

``` bash
php artisan vendor:publish
```

or

``` bash
php artisan vendor:publish --provider="Jenky\Guzzilla\GuzzillaServiceProvider"
```

## Configuration

After publishing Guzzilla's assets, its primary configuration file will be located at `config/guzzilla.php`. This configuration file allows you to configure your guzzle client options and each configuration option includes a description of its purpose, so be sure to thoroughly explore this file.

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
    'handler' => App\Http\CustomGuzzleHandler::class,
    'with' => [
        'delay' => 5,
    ],
],
```

An alternative way is set the handler in the [`options`](#configure-the-guzzle-option) configuration:

``` php
'default' => [
    'options' => [
        'handler' => App\Http\CustomGuzzleHandler::create(['delay' => 5]),
    ],
],
```

### Configure the guzzle middleware

Configure guzzle [Middleware](http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#middleware) within the channel.

``` php
'default' => [
    'middleware' => [
        Jenky\Guzzilla\Middleware\RequestEvent::class,
    ],
],
```

> The package ships with 2 middleware. You can read about the middleware in the [middleware](#middleware) section.


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
use GuzzleHttp\Middleware;

class CustomizeHandlerStack
{
    /**
     * Customize the given handler stack instance.
     *
     * @param  \GuzzleHttp\HandlerStack  $stack
     * @return void
     */
    public function __invoke($stack)
    {
        $stack->before('add_foo', Middleware::mapRequest(function (RequestInterface $r) {
            return $r->withHeader('X-Baz', 'Qux');
        }, 'add_baz');
    }
}
```

> All of your "tap" classes are resolved by the service container, so any constructor dependencies they require will automatically be injected.

## Middleware

### `RequestEvent`

This middleware will fire `Jenky\Guzzilla\Events\RequestHandled` event when a request had been fulfilled. It has these properties:

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

This middleware allows you to use custom response handler instead of `GuzzleHttp\Psr7\Response`. By default it will use `Jenky\Guzzilla\Response` which is a child class of `GuzzleHttp\Psr7\Response`. However you can configure the middleware to use your own implementation.

``` php
'default' => [
    'middleware' => [
        Jenky\Guzzilla\Middleware\ResponseHandler::class => [
            'response' => Jenky\Guzzilla\JsonResponse::class
        ],
    ],
],
```

If you needs more parameters, extends the `ResponseHandler` with your middleware and receive your params in `__construct()`.

## Usage

``` php
use Jenky\Guzzila\Facades\Guzzle;

Guzzle::get('https://jsonplaceholder.typicode.com/users');
// or using helper
guzzle()->get('https://jsonplaceholder.typicode.com/users');
```

Sometimes you may wish to send a request to a channel other than your application's default channel. You may use the `channel` method on the `Guzzle` facade to retrieve and send to any channel defined in your configuration file:

``` php
use Jenky\Guzzila\Facades\Guzzle;

Guzzle::channel('gitlab')->get('https://jsonplaceholder.typicode.com/users');
// or using helper
guzzle('gitlab')->get('https://jsonplaceholder.typicode.com/users');
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

[ico-version]: https://img.shields.io/packagist/v/jenky/guzzilla.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/jenky/guzzilla/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/jenky/guzzilla.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/jenky/guzzilla.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/jenky/guzzilla.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jenky/guzzilla
[link-travis]: https://travis-ci.org/jenky/guzzilla
[link-scrutinizer]: https://scrutinizer-ci.com/g/jenky/guzzilla/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/jenky/guzzilla
[link-downloads]: https://packagist.org/packages/jenky/guzzilla
[link-author]: https://github.com/jenky
[link-contributors]: ../../contributors
