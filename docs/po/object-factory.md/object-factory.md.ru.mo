��          \      �       �      �     �   G   �     8  �   M  �   G  A  �  n  9     �  �  �  �   �  :   v	  �  �	  `  @  A  �                                       Object Factory The hydrator uses `ObjectFactoryInterface` implementation to create object when you use `Hydrator::create()` method: hydrator passes resolved constructor arguments to factory and obtains created object for next hydration.  The package provides two implementations out of the box: To use non-default object factory, pass it to the hydrator constructor: Using object factory `ContainerObjectFactory`. Uses [Yii Injector](https://github.com/yiisoft/injector) to create object that allow to use [PSR-11](https://www.php-fig.org/psr/psr-11/) compatible DI container to resolve constructor argument not resolved by the hydrator. `ReflectionObjectFactory`. Uses reflection to create object. It cannot create objects when some constructor arguments aren't resolved.  This object factory is used by default. use Yiisoft\Injector\Injector;
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
 Project-Id-Version: 
PO-Revision-Date: 2025-03-01 21:53+0500
Last-Translator: Automatically generated
Language-Team: none
Language: ru
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);
X-Generator: Poedit 3.5
 Фабрика объектов Гидратор использует реализацию `ObjectFactoryInterface` для создания объекта, когда вы вызываете метод `Hydrator::create()`: гидратор передает разрешенные аргументы конструктора фабрике и получает созданный объект для последующего наполнения. Пакет предоставляет две реализации из коробки: Чтобы использовать фабрику объектов, отличную от стандартной, передайте ее в конструктор гидратора: Использование фабрики объектов `ContainerObjectFactory`. Использует [Yii Injector](https://github.com/yiisoft/injector) для создания объектов и может использовать совместимый с [PSR-11](https://www.php-fig.org/psr/psr-11/) DI-контейнер для разрешения аргументов конструктора, не разрешенных в гидраторе. `ReflectionObjectFactory`. Использует рефлексию для создания объекта. Она не может создавать объекты, когда некоторые аргументы конструктора не разрешены. Эта фабрика объектов используется по-умолчанию. use Yiisoft\Injector\Injector;
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
 