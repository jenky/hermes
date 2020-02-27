# Changelog

All notable changes to `hermes` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

<!-- ## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed -->

## [UNRELEASED]

### Added
- Add some syntax sugar methods to interact with HTTP response message

### Changed
- Use splat operator for `array_merge_recursive_unique` instead of `func_get_args()`
- Use `Jenky\Hermes\Transformable` interface for response handler that needs to parse response body to native type

## [1.1.1](https://github.com/jenky/hermes/compare/1.1.0...1.1.1) - 2020-02-25

### Fixed
- Fixed merges config recursively

### Changed
- `ResponseHandler` interceptor constructor now accepts class name instead of `ResponseHandlerInterface` instance

## [1.1.0](https://github.com/jenky/hermes/compare/1.0.2...1.1.0) - 2020-02-17

### Added
- Prepare for Laravel 7 and Guzzle 7

### Changed
- Mutable client by passing `$options` argument

## [1.0.2](https://github.com/jenky/hermes/compare/1.0.1...1.0.2) - 2020-02-13

### Fixed
- Some bugs

## [1.0.1](https://github.com/jenky/hermes/compare/1.0.0...1.0.1) - 2019-12-24

### Added
- Json driver

### Fixed
- Some bugs
- Fixes response handler

## [1.0.0](https://github.com/jenky/hermes/tree/1.0.1) - 2019-11-04

### Added
- Initial release
