��    	      d      �       �      �     �   �     S   �        �   #  �   �  I  C  n  �  :   �  �  7  �   �  �   �	  U   �
  �     �   �  I  �                    	                          Attribute resolver factory Default factory used depends on the environment. When using hydrator package within the Yii ecosystem (an application uses [Yii Config](https://github.com/yiisoft/config)), default is `ContainerAttributeResolverFactory`. Otherwise, it is `ReflectionAttributeResolverFactory`. The hydrator uses `AttributeResolverFactoryInterface` implementation to create attribute resolvers.  The package provides two implementations out of the box: To use non-default attribute resolver factory, pass it to the hydrator constructor: Using attribute resolver factory `ContainerAttributeResolverFactory`. Uses [PSR-11](https://www.php-fig.org/psr/psr-11/) compatible DI container to create attribute resolver. `ReflectionAttributeResolverFactory`. Uses reflection to create attribute resolver, and can create attribute resolvers without dependencies only. use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;

/**
 * @var Psr\Container\ContainerInterface $container
 */ 
$attributeResolverFactory = new ContainerAttributeResolverFactory($container);

$hydrator = new Hydrator(
    attributeResolverFactory: $attributeResolverFactory,
    // ...
);
 Project-Id-Version: 
PO-Revision-Date: 2025-03-01 20:53+0500
Last-Translator: Automatically generated
Language-Team: none
Language: ru
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);
X-Generator: Poedit 3.5
 Фабрика обработчиков атрибутов Используемая по-умолчанию фабрика зависит от среды. Когда пакет гидратора работает внутри экосистемы Yii (приложение использует [Yii Config](https://github.com/yiisoft/config)) используется `ContainerAttributeResolverFactory`. В других случаях используется `ReflectionAttributeResolverFactory`. Гидратор использует реализацию `AttributeResolverFactoryInterface` для создания обработчиков атрибутов. Пакет предоставляет две реализации из коробки: Чтобы использовать фабрику обработчиков атрибутов, отличную от стандартной, передайте ее в конструктор гидратора: Использование фабрики обработчиков атрибутов `ContainerAttributeResolverFactory`. Использует совместимый с [PSR-11](https://www.php-fig.org/psr/psr-11/) DI-контейнер для создания обработчика атрибутов. `ReflectionAttributeResolverFactory`. Использует рефлексию для создания обработчика атрибута и может создавать обработчики только без зависимостей. use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;

/**
 * @var Psr\Container\ContainerInterface $container
 */ 
$attributeResolverFactory = new ContainerAttributeResolverFactory($container);

$hydrator = new Hydrator(
    attributeResolverFactory: $attributeResolverFactory,
    // ...
);
 