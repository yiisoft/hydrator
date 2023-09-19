<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Hydrator</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/hydrator/v/stable.png)](https://packagist.org/packages/yiisoft/hydrator)
[![Total Downloads](https://poser.pugx.org/yiisoft/hydrator/downloads.png)](https://packagist.org/packages/yiisoft/hydrator)
[![Build status](https://github.com/yiisoft/hydrator/workflows/build/badge.svg)](https://github.com/yiisoft/hydrator/actions?query=workflow%3Abuild)
[![Code Coverage](https://codecov.io/gh/yiisoft/hydrator/branch/master/graph/badge.svg)](https://codecov.io/gh/yiisoft/hydrator)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fhydrator%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/hydrator/master)
[![static analysis](https://github.com/yiisoft/hydrator/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/hydrator/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/hydrator/coverage.svg)](https://shepherd.dev/github/yiisoft/hydrator)
[![psalm-level](https://shepherd.dev/github/yiisoft/hydrator/level.svg)](https://shepherd.dev/github/yiisoft/hydrator)

The package provides a way to create or hydrate objects from a set of raw data.

Features are:

- supports properties of any visibility;
- uses constructor arguments to create objects;
- resolves dependencies when creating objects using [PSR-11](http://www.php-fig.org/psr/psr-11/) compatible DI container
  provided;
- supports nested objects;
- supports mapping;
- allows fine-tuning hydration via PHP attributes.

## Basic usage

To hydrate existing object:

```php
use Yiisoft\Hydrator\Hydrator;

$hydrator = new Hydrator();
$hydrator->hydrate($object, $data);
```

To create a new object and fill it with the data: 

```php
use Yiisoft\Hydrator\Hydrator;

$hydrator = new Hydrator();
$object = $hydrator->create(MyClass::class, $data);
```

To pass arguments to the constructor of a nested object, use nested array or dot-notation:

```php
final class Engine
{
    public function __construct(
        private string $name,
    ) {}
}

final class Car
{
    public function __construct(
        private string $name,
        private Engine $engine,
    ) {}
}

// nested array
$object = $hydrator->create(Car::class, [
    'name' => 'Ferrari',
    'engine' => [
        'name' => 'V8',
    ]
]);

// or dot-notation
$object = $hydrator->create(Car::class, [
    'name' => 'Ferrari',
    'engine.name' => 'V8',
]);
```

That would pass the `name` constructor argument of the `Car` object and create a new `Engine` object for `engine`
argument passing `V8` as the `name` argument to its constructor.

## Configuration with PHP attributes

You can configure how the hydrator creates or hydrates a specific class using attributes. 

### Mapping 

To map attributes to specific data keys, use `Map` attribute:

```php
use \Yiisoft\Hydrator\Attribute\Data\Map;

#[Map([
    'firstName' => 'first_name',
    'lastName' => 'last_name',
])]
final class Person
{
    public function __construct(
        private string $firstName,
        private string $lastName,
    ) {}
}

$person = $hydrator->create(Person::class, [
    'first_name' => 'John',
    'last_name' => 'Doe',
]);
```

When using the `Map`, you can set `strict` argument to `true`. That instructs the hydrator that all data should be 
mapped explicitly.

Alternatively you can map each property using `Data` attribute:

```php
use \Yiisoft\Hydrator\Attribute\Parameter\Data;

final class Person
{
    public function __construct(
        #[Data('first_name')]
        private string $firstName,
        #[Data('last_name')]
        private string $lastName,
    ) {}
}

$person = $hydrator->create(Person::class, [
    'first_name' => 'John',
    'last_name' => 'Doe',
]);
```

### Casting value to string

To cast a value to string, use `ToString` attribute:

```php
use \Yiisoft\Hydrator\Attribute\Parameter\ToString;

class Money
{
    public function __construct(
        #[ToString]
        private string $value,
        private string $currency,
    ) {}
}

$money = $hydrator->create(Money::class, [
    'value' => 4200,
    'currency' => 'AMD',
]);
```

### Skipping hydration

To skip hydration of a specific property, use `SkipHydration` attribute:

```php
use \Yiisoft\Hydrator\Attribute\SkipHydration;

class MyClass
{
    #[SkipHydration]
    private $property;
}
```

### Resolving dependencies

To resolve dependencies by specific ID using DI container, use `Di` attribute:

```php
ues \Yiisoft\Hydrator\Attribute\Parameter\Di;

class MyClass
{
    public function __construct(
        #[Di(id: 'importConnection')]
        private ConnectionInterface $connection,
    ) {}
}
```

The annotation will instruct hydrator to get `$connection` from DI container by `importConnection` ID.

### Your own attributes

There are two main parts:

- Attribute class.
  It only stores configuration options and a reference to its handler.
- Attribute resolver.
  Given an attribute reflection and extra data, it resolves an attribute.

Besides responsibilities' separation,
this approach allows the package to automatically resolve dependencies for attribute resolver.

#### Data attributes

You apply data attributes to a whole class.
The main goal is getting data from external sources such as from request.
Additionally, it's possible to specify how external source attributes map to hydrated class.

Data attribute class should implement `DataAttributeInterface` and the corresponding data attribute resolver should
implement `DataAttributeResolverInterface`.

#### Parameter attributes

You apply parameter attributes to class properties and constructor parameters.
You use these attributes for getting value for specific parameter or for preparing the value
(for example, by type casting).

Parameter attribute class should implement `ParameterAttributeInterface` and the corresponding parameter attribute
resolver should implement `ParameterAttributeResolverInterface`.

## Requirements

- PHP 8.0 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/hydrator
```

## General usage

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

### Code style

Use [Rector](https://github.com/rectorphp/rector) to make codebase follow some specific rules or 
use either newest or any specific version of PHP: 

```shell
./vendor/bin/rector
```

### Dependencies

Use [ComposerRequireChecker](https://github.com/maglnet/ComposerRequireChecker) to detect transitive 
[Composer](https://getcomposer.org/) dependencies.

## License

The Yii Hydrator is free software. It's released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
