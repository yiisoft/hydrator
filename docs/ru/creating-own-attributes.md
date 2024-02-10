# Creating own attributes
# Создание собственных атрибутов

There are two main parts of an attribute:
Атрибут состоит из двух основных частей:

- **Attribute class**. It only stores configuration options and a reference to its handler.
- **Класс атрибута**. В нем хранятся только параметры конфигурации и ссылка на свой обработчик.
- **Attribute resolver**. Given an attribute reflection and extra data, it resolves an attribute.
- **Сопостовитель атрибутов**. Учитывая рефлексию и дополнительные данные, он сопоставляет атрибут.

Besides responsibilities' separation, this approach allows the package to automatically resolve dependencies for 
attribute resolver.
Помимо разделения ответственности, такой подход позволяет пакету автоматически разрешать зависимости для сопостовителя атрибутов.

## Data attributes
## Атрибуты данных

You apply data attributes to a whole class.The main goal is getting data from external sources such as from request.
Additionally, it's possible to specify how external source attributes map to hydrated class.
Атрибуты данных применяются ко всему классу. Основная цель - получение данных из внешних источников, например, из запроса. Кроме того, можно указать, как атрибуты внешнего источника сопоставляются с гидратированным классом.

Data attribute class should implement `DataAttributeInterface` and the corresponding data attribute resolver should
implement `DataAttributeResolverInterface`.
Класс атрибутов данных должен реализовывать `DataAttributeInterface`, а соответствующий сопостовитель атрибутов должен реализовывать `DataAttributeResolverInterface`.

### Example of custom data attribute
### Пример пользовательского атрибута данных

For example, let's create a data attribute that uses predefined array as data for object populating.
Для примера давайте создадим атрибут данных, который использует предопределенный массив в качестве данных для заполнения объекта.

Атрибут:

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

Сопоставитель:

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

## Parameter attributes
## Атрибуты параметров

You apply parameter attributes to class properties and constructor parameters. You use these attributes for getting 
value for specific parameter or for preparing the value (for example, by type casting).
Атрибуты параметров применяются к свойствам класса и параметрам конструктора. Эти атрибуты используются для получения значения конкретного параметра или для подготовки значения (например, приведения типов)

Parameter attribute class should implement `ParameterAttributeInterface` and the corresponding parameter attribute
resolver should implement `ParameterAttributeResolverInterface`.
Класс параметра атрибутов должен реализовывать `ParameterAttributeInterface`, а соответствующий параметр атрибута сопостовителя атрибутов должен реализовывать `ParameterAttributeResolverInterface`.

### Example of custom parameter attribute
### Пример пользовательского параметра атрибута

For example, let's create a parameter attribute that provides a random value for object property.
Например, давайте создадим параметр атрибута, который предоставляет случайное значение для свойства объекта.

Атрибут:

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

Сопоставитель:

```php
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Result;

final class RandomIntResolver implements ParameterAttributeResolverInterface
{
    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        ParameterAttributeResolveContext $context
    ): Result {
        if (!$attribute instanceof RandomInt) {
            throw new UnexpectedAttributeException(RandomInt::class, $attribute);
        }

        $value = rand($attribute->getMin(), $attribute->getMax());

        return Result::success($value);
    }
}
```

## Using a single class for both attribute and resolver
## Использование одного класса как для атрибута, так и для сопостовителя

If your attribute is simple and doesn't require dependencies, you can combine attribute and its resolver in a single class.
Если ваши атрибуты простые и не требуют зависимостей, вы можете скомбинировать атрибуты и их сопоставитель в одном классе.

For example:
Например:

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
