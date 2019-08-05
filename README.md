# guzzilla

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


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
        'base_uri' => 'https://api.github.com/v1',
        'time_out' => 20,
    ],
],
```

### Configure the guzzle handler
WIP

### Configure the guzzle middleware

Set guzzle middleware options within the channel. Please visit [Middleware](http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#middleware) for more information.

``` php
'default' => [
    'middleware' => [
        Jenky\Guzzilla\Middleware\RequestEvent::class,
    ],
],
```

By default, the package ships with 2 middleware. You can read about the middleware in the [middleware](#middleware) section.


### Customizing the guzzle handler stack

Sometimes you may need complete control over how guzzle's handler stack is configured for an existing channel. For example, you may want to add, remove or reorder a middleware for a given channel's handler stack. Please visit [HandleStack](http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#handlerstack) for more information.

To get started, define a tap array on the channel's configuration. The tap array should contain a list of classes that should have an opportunity to customize (or "tap" into) the handle stack instance after it is created:

``` php
'default' => [
    'tap' => [
        App\Middleware\AddRequestLogHandler::class,
    ],
],
```

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
Guzzle::get('https://jsonplaceholder.typicode.com/users');
// or using helper
guzzle()->get('https://jsonplaceholder.typicode.com/users');
```

Sometimes you may wish to send a request to a channel other than your application's default channel. You may use the `channel` method on the `Guzzle` facade to retrieve and send to any channel defined in your configuration file:

``` php
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
