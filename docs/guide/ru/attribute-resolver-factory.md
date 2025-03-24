# Фабрика обработчиков атрибутов

The hydrator 1 uses `AttributeResolverFactoryInterface` implementation to
create attribute resolvers.  The package provides two implementations out of
the box:

- `ReflectionAttributeResolverFactory`. Использует рефлексию для создания
  обработчика атрибута и может создавать обработчики только без
  зависимостей.
- `ContainerAttributeResolverFactory`. Использует совместимый с
  [PSR-11](https://www.php-fig.org/psr/psr-11/) DI-контейнер для создания
  обработчика атрибутов.

Используемая по-умолчанию фабрика зависит от среды. Когда пакет гидратора
работает внутри экосистемы Yii (приложение использует [Yii
Config](https://github.com/yiisoft/config)) используется
`ContainerAttributeResolverFactory`. В других случаях используется
`ReflectionAttributeResolverFactory`.

## Использование фабрики обработчиков атрибутов

Чтобы использовать фабрику обработчиков атрибутов, отличную от стандартной,
передайте ее в конструктор гидратора:

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
