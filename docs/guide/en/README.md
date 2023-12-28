# Yii Hydrator

The package provides a way to create and hydrate objects from a set of raw data.

## Configuration with PHP attributes

You can configure how the hydrator creates or hydrates a specific class using attributes.

### Mapping

To map attributes to specific data keys, use `Data` attribute:

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
