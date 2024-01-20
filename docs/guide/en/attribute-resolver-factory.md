# Attribute resolver factory

The hydrator uses `AttributeResolverFactoryInterface` implementation to create attribute resolvers. It supports two 
implementations out of the box.

## Attribute resolver factories out of the box

### `ReflectionAttributeResolverFactory` 

It uses reflection to create attribute resolver, and can create attribute resolvers without dependencies only.

### `ContainerAttributeResolverFactory`

It uses PSR-11 compatible DI container to create attribute resolver.

## Default attribute resolver factory

It depends on environment. When using hydrator package within the Yii ecosystem (an application uses
[Yii Config](https://github.com/yiisoft/config)), by default uses `ContainerAttributeResolverFactory`. Otherwise, it uses `ReflectionAttributeResolverFactory`.

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
