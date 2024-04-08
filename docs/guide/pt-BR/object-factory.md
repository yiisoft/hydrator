# Object Factory (Fábrica de Objetos)

O hydrator usa a implementação `ObjectFactoryInterface` para criar o objeto quando você usa o método `Hydrator::create()`:
O hydrator passa os argumentos do construtor resolvidos para o factory e obtém o objeto criado para a próxima hidratação.
O pacote fornece duas implementações prontas para uso:

- `ReflectionObjectFactory`. Usa Reflection para criar objetos. Não é possível criar objetos quando alguns argumentos do construtor
não estão resolvidos. Este object factory é usado por padrão.
- `ContainerObjectFactory`. Usa [Yii Injector](https://github.com/yiisoft/injector) para criar objetos que permitem usar o
[PSR-11](https://www.php-fig.org/psr/psr-11/) um contêiner DI compatível para resolver argumentos do construtor não resolvidos
pelo hydrator.

## Usando o object factory

Para usar o object factory não padrão, passe-o para o construtor do hydrator:

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
