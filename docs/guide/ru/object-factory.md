# Object Factory

The hydrator uses `ObjectFactoryInterface` implementation to create object when you use `Hydrator::create()` method:
hydrator passes resolved constructor arguments to factory and obtains created object for next hydration.
The package provides two implementations out of the box:

- `ReflectionObjectFactory`. Uses reflection to create object. It cannot create objects when some constructor arguments
  aren't resolved.  This object factory is used by default.
- `ContainerObjectFactory`. Uses [Yii Injector](https://github.com/yiisoft/injector) to create object that allow to use
  [PSR-11](https://www.php-fig.org/psr/psr-11/) compatible DI container to resolve constructor argument not resolved
  by the hydrator.

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
