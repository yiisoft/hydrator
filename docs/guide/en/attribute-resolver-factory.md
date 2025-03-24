# Attribute resolver factory

The hydrator 1 uses `AttributeResolverFactoryInterface` implementation to create attribute resolvers.
The package provides two implementations out of the box:

- `ReflectionAttributeResolverFactory`. Uses reflection to create attribute resolver, and can create attribute resolvers
  without dependencies only.
- `ContainerAttributeResolverFactory`. Uses [PSR-11](https://www.php-fig.org/psr/psr-11/) compatible DI container
  to create attribute resolver.

Default factory used depends on the environment. When using hydrator package within the Yii ecosystem (an application
uses [Yii Config](https://github.com/yiisoft/config)), default is `ContainerAttributeResolverFactory`. Otherwise,
it is `ReflectionAttributeResolverFactory`.

## Using attribute resolver factory

To use non-default attribute resolver factory, pass it to the hydrator constructor:

```php
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;

/**
 * @var Psr\Container\ContainerInterface $container
 */ 
$attributeResolverFactory = new ContainerAttributeResolverFactory($container);

$hydrator = new Hydrator(
    attributeResolverFactory: $attributeResolverFactory,
    // ...
);
```
