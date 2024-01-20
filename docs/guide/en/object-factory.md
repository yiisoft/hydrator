# Object Factory

The hydrator uses `ObjectFactoryInterface` implementation to create object when you uses `Hydrator::create()` method: hydrator passes to factory resolved constructor arguments and gets created object for next hydration. The package provides two implementations out of the box.

## Object factories out of the box

### `ReflectionObjectFactory`

It uses reflection to create object, and cannot create objects when not all constructor arguments are resolved.
This object factory is used by default.

### `ContainerObjectFactory`

It uses [Yii Injector](https://github.com/yiisoft/injector) to create object that allow to use PSR-11 compatible 
DI container for resolve constructor arguments, which were not resolved by the hydrator.

## Using object factory

To use non-default object factory, pass it to the hydrator constructor:

```php
use Yiisoft\Injector\Injector;
use Yiisoft\Hydrator\ObjectFactory\ContainerObjectFactory;

/**
 * @var Psr\Container\ContainerInterface $container
 */ 
$injector = new Injector($container)
$objectFactory = new ContainerObjectFactory($injector);

$hydrator = new Hydrator(
    objectFactory: $objectFactory,
    // ...
);
```
