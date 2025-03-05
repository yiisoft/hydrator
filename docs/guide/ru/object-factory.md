# Фабрика объектов

Гидратор использует реализацию `ObjectFactoryInterface` для создания
объекта, когда вы вызываете метод `Hydrator::create()`: гидратор передает
разрешенные аргументы конструктора фабрике и получает созданный объект для
последующего наполнения. Пакет предоставляет две реализации из коробки:

- `ReflectionObjectFactory`. Использует рефлексию для создания объекта. Она
  не может создавать объекты, когда некоторые аргументы конструктора не
  разрешены. Эта фабрика объектов используется по-умолчанию.
- `ContainerObjectFactory`. Использует [Yii
  Injector](https://github.com/yiisoft/injector) для создания объектов и
  может использовать совместимый с
  [PSR-11](https://www.php-fig.org/psr/psr-11/) DI-контейнер для разрешения
  аргументов конструктора, не разрешенных в гидраторе.

## Использование фабрики объектов

Чтобы использовать фабрику объектов, отличную от стандартной, передайте ее в
конструктор гидратора:

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
