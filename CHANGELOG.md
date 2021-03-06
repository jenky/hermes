# Changelog

All notable changes to `hermes` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

<!-- ## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed -->

## [1.4.1](https://github.com/jenky/hermes/compare/1.4.0...1.4.1) - 2020-12-11

### Added
- Support PHP 8.

## [1.4.0](https://github.com/jenky/hermes/compare/1.3.2...1.4.0) - 2020-09-09

### Added
- Support Laravel 8.

## [1.3.2](https://github.com/jenky/hermes/compare/1.3.1...1.3.2) - 2020-05-11

### Fixed
- Fixed `isError` check.

## [1.3.1](https://github.com/jenky/hermes/compare/1.3.0...1.3.1) - 2020-04-19

### Removed
- Remove lazy evaluation code.

## [1.3.0](https://github.com/jenky/hermes/compare/1.2.2...1.3.0) - 2020-04-17

### Deprecated
- Rename `Interceptors` to `Middleware`.

### Removed
- Remove `lazy` evaluation.

## [1.2.2](https://github.com/jenky/hermes/compare/1.2.1...1.2.2) - 2020-03-06

### Deprecated
- Deprecated lazy evaluation.

### Fixed
- Fix error with `tap` when first parameter always is an empty string when not passed.

## [1.2.1](https://github.com/jenky/hermes/compare/1.2.0...1.2.1) - 2020-03-03

### Fixed
- Fix test cases for Laravel 7.

## [1.2.0](https://github.com/jenky/hermes/compare/1.1.1...1.2.0) - 2020-02-28

### Added
- Add some syntax sugar methods to interact with HTTP response message.
- Lazy evaluation to resolve container binding inside config file.

### Changed
- Rename `array_merge_recursive_unique` to `array_merge_recursive_distinct` and move it to `functions.php` file that's using namespace.
- Use splat operator for `array_merge_recursive_distinct` instead of `func_get_args()`.
- Use `Jenky\Hermes\Parsable` interface for response handler that needs to parse response body and casts to native type.

## [1.1.1](https://github.com/jenky/hermes/compare/1.1.0...1.1.1) - 2020-02-25

### Fixed
- Fixed merges config recursively.
- Fix callable middleware.

### Changed
- `ResponseHandler` interceptor constructor now accepts class name instead of `ResponseHandlerInterface` instance.

## [1.1.0](https://github.com/jenky/hermes/compare/1.0.2...1.1.0) - 2020-02-17

### Added
- Prepare for Laravel 7 and Guzzle 7.

### Changed
- Mutable client by passing `$options` as second parameter to `guzzle()` helper or `channel()` method.

## [1.0.2](https://github.com/jenky/hermes/compare/1.0.1...1.0.2) - 2020-02-13

### Fixed
- Some bugs.

## [1.0.1](https://github.com/jenky/hermes/compare/1.0.0...1.0.1) - 2019-12-24

### Added
- Json driver.

### Fixed
- Some bugs.
- Fixes response handler.

## [1.0.0](https://github.com/jenky/hermes/tree/1.0.1) - 2019-11-04

### Added
- Initial release.
