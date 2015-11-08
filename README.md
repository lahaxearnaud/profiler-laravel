# Laravel Profiler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Build Status][ico-scrutinizer]][link-scrutinizer]
[![Build Status][ico-coverage]][link-coverage]


[![Build Status][ico-ndrx]][link-ndrx]


## Install

Via Composer

``` bash
$ composer require ndrx-io/profiler-laravel
```

## Usage

### Register provider

``` php
'providers' => [
    // Other Service Providers
    \Ndrx\Profiler\Laravel\LaravelProfilerServiceProvider::class
]
```

### Register facade

``` php
'aliases' => [
    // Other aliases
    'Profiler' => \Ndrx\Profiler\Laravel\ProfilerFacade::class
]
```
### Publish config

``` bash
$ php artisan vendor:publish
```

### 

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email arnaud.lahaxe[at]versusmind.eu instead of using the issue tracker.

## Credits

- [LAHAXE Arnaud][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ndrx/profiler-laravel.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ndrx-io/profiler-laravel/master.svg?style=flat-square
[ico-ndrx]: https://pbs.twimg.com/profile_images/585415130881642497/Qg4niE0o.png
[ico-scrutinizer]: https://scrutinizer-ci.com/g/ndrx-io/profiler-laravel/badges/quality-score.png?b=master
[ico-coverage]: https://scrutinizer-ci.com/g/ndrx-io/profiler-laravel/badges/coverage.png?b=master


[link-packagist]: https://packagist.org/packages/ndrx-io/profiler-laravel
[link-travis]: https://travis-ci.org/ndrx-io/profiler-laravel
[link-author]: https://github.com/lahaxearnaud
[link-contributors]: ../../contributors
[link-ndrx]: http://ndrx.io
[link-scrutinizer]: https://scrutinizer-ci.com/g/ndrx-io/profiler-laravel/
[link-coverage]: https://scrutinizer-ci.com/g/ndrx-io/profiler-laravel/
