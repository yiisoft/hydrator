# Создание собственных атрибутов

Атрибут состоит из двух основных частей:

- **Класс атрибута**. В нем хранятся только параметры конфигурации и ссылка на свой обработчик.
- **Обработчик атрибутов**. Учитывая рефлексию и дополнительные данные, он определяет атрибут.

Помимо разделения ответственности, такой подход позволяет пакету автоматически разрешать зависимости для обработчика атрибутов.

## Атрибуты данных

Атрибуты данных применяются ко всему классу. Основная цель - получение данных из внешних источников, например, из запроса.
Кроме того, можно указать, как атрибуты внешнего источника сопоставляются с гидратированным классом.

Класс атрибутов данных должен реализовывать `DataAttributeInterface`, а соответствующий обработчик атрибутов должен реализовывать `DataAttributeResolverInterface`.

### Пример пользовательского атрибута данных

Для примера давайте создадим атрибут данных, который использует предопределенный (переданный ??) массив в качестве данных для заполнения объекта.

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

Обработчик:

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

## Атрибуты параметров

Атрибуты параметров применяются к свойствам класса и параметрам конструктора. Эти атрибуты используются для получения значения конкретного параметра или для подготовки значения (например, приведения типов)

Класс параметра атрибутов должен реализовывать `ParameterAttributeInterface`, а соответствующий обработчик атрибутов должен реализовывать `ParameterAttributeResolverInterface`.

### Пример пользовательского атрибута параметра

Например, давайте создадим атрибут параметра, который возвращает случайное значение для свойства объекта.

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

Обработчик:

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

## Использование одного класса как для атрибута, так и для обработчика

Если ваши атрибуты простые и не требуют зависимостей, вы можете скомбинировать атрибуты и их обработчик в одном классе.

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
