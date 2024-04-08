# Fábrica de resolvedores de atributos

O hydrator usa a implementação `AttributeResolverFactoryInterface` para criar resolvedores de atributos.
O pacote fornece duas implementações prontas para uso:

- `ReflectionAttributeResolverFactory`. Usa Reflection para criar resolvedores de atributos e pode criar resolvedores de atributos
apenas sem dependências.
- `ContainerAttributeResolverFactory`. Usa Container DI compatível com [PSR-11](https://www.php-fig.org/psr/psr-11/)
para criar o resolvedor de atributos.

O padrão de factory usado depende do ambiente. Ao usar o pacote hydrator dentro do ecossistema Yii (um aplicativo
usa [Yii Config](https://github.com/yiisoft/config)), o padrão é `ContainerAttributeResolverFactory`. De outra forma,
é `ReflectionAttributeResolverFactory`.

## Usando factory de resolução de atributos

Para usar o resolvedor de atributos não padrão, passe-o para o construtor do hydrator:

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
