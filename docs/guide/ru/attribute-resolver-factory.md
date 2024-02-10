# Фабрика сопоставителей атрибутов

Гидратор использует реализацию `AttributeResolverFactoryInterface` для создания сопоставителей атрибутов.
Пакет предоставляет две реализации "из коробки":

- `ReflectionAttributeResolverFactory`. Использует рефлексию для создания сопоставителя атрибута и может создавать сопоставителя атрибутов только без зависимостей.
- `ContainerAttributeResolverFactory`. Использует совместимый с [PSR-11](https://www.php-fig.org/psr/psr-11/) DI-контейнер для создания сопоставителя атрибутов

Используемая по-умолчанию фабрика зависит от среды. Когда пакет гидратора работает внутри экосистемы Yii (приложение использует [Yii Config](https://github.com/yiisoft/config)) используется `ContainerAttributeResolverFactory`. В других случаях используется `ReflectionAttributeResolverFactory`.

## Использование фабрики сопоставителей атрибутов

Чтобы использовать фабрику сопоставителей атрибутов, отличную от стандартной, передайте ее в конструктор гидратора:

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
