# Criando atributos próprios

Existem duas partes principais de um atributo:

- **Attribute class** (Classe de atributos). Ele armazena apenas opções de configuração e uma referência ao seu manipulador.
- **Attribute resolver** (Resolvedor de atributos). Dada um atributo reflection e dados extras, ele resolve um atributo.

Além da separação de responsabilidades, esta abordagem permite que o pacote resolva automaticamente dependências para
o resolvedor de atributos.

## Atributos de dados

Você aplica atributos de dados a uma classe inteira. O objetivo principal é obter dados de fontes externas, como solicitações.
Além disso, é possível especificar como os atributos de origem externa são mapeados para a classe hidratada.

A classe de atributos de dados deve implementar `DataAttributeInterface` e o resolvedor de atributos de dados correspondente deve
implementar `DataAttributeResolverInterface`.

### Exemplo de atributo de dados personalizado

Por exemplo, vamos criar um atributo de dados que usa um array predefinido "data" para preencher o objeto.

Atributo:

```php
use Attribute;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final class FromArray implements DataAttributeInterface
{
    public function __construct(
        private array $data,
        ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getResolver(): string
    {
        return FromArrayResolver::class;
    }
}
```

Resolver:

```php
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeResolverInterface;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\DataInterface;

final class FromArrayResolver implements DataAttributeResolverInterface
{
    public function prepareData(DataAttributeInterface $attribute, DataInterface $data): DataInterface
    {
        if (!$attribute instanceof FromArray) {
            throw new UnexpectedAttributeException(FromArray::class, $attribute);
        }

        return new ArrayData($attribute->getData());
    }
}
```

## Atributos de parâmetro

Você aplica atributos de parâmetro às propriedades de classe e parâmetros de construtor. Você usa esses atributos para obter
valor para um parâmetro específico ou para preparar o valor (por exemplo, por conversão de tipo).

A classe de atributo de parâmetro deve implementar `ParameterAttributeInterface` e o atributo de parâmetro correspondente
o resolvedor deve implementar `ParameterAttributeResolverInterface`.

### Exemplo de atributo de parâmetro personalizado

Por exemplo, vamos criar um atributo de parâmetro que forneça um valor aleatório para a propriedade do objeto.

Atributo:

```php
use Attribute;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class RandomInt implements ParameterAttributeInterface
{
    public function __construct(
    private int $min = 0,
    private int $max = 99,
    ) {
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getResolver(): string
    {
        return RandomIntResolver::class;
    }
}
```

Resolver:

```php
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeResolverInterface;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\DataInterface;

final class FromArrayResolver implements DataAttributeResolverInterface
{
    public function prepareData(DataAttributeInterface $attribute, DataInterface $data): DataInterface
    {
        if (!$attribute instanceof FromArray) {
            throw new UnexpectedAttributeException(FromArray::class, $attribute);
        }

        return new ArrayData($attribute->getData());
    }
}
```

## Usando uma única classe para atributo e resolvedor

Se o seu atributo for simples e não exigir dependências, você poderá combinar o atributo e seu resolvedor em uma única classe.
Por exemplo:

```php
use Attribute;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeResolverInterface;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\DataInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final class FromArray implements DataAttributeInterface, DataAttributeResolverInterface
{
    public function __construct(
        private array $data,
        ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getResolver(): self
    {
        return $this;
    }

    public function prepareData(DataAttributeInterface $attribute, DataInterface $data): DataInterface
    {
        if (!$attribute instanceof FromArray) {
            throw new UnexpectedAttributeException(FromArray::class, $attribute);
        }

        return new ArrayData($attribute->getData());
    }
}
```
