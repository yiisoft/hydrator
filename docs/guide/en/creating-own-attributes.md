# Creating own attributes

There are two main parts of an attribute:

- **Attribute class**. It only stores configuration options and a reference to its handler.
- **Attribute resolver**. Given an attribute reflection and extra data, it resolves an attribute.

Besides responsibilities' separation, this approach allows the package to automatically resolve dependencies for 
attribute resolver.

## Data attributes

You apply data attributes to a whole class. The main goal is getting data from external sources such as from request.
Additionally, it's possible to specify how external source attributes map to hydrated class.

Data attribute class should implement `DataAttributeInterface` and the corresponding data attribute resolver should
implement `DataAttributeResolverInterface`.

### Example of custom data attribute

For example, let's create data attribute that use predefined array as data for object populating.

Attribute:

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

## Parameter attributes

You apply parameter attributes to class properties and constructor parameters. You use these attributes for getting 
value for specific parameter or for preparing the value (for example, by type casting).

Parameter attribute class should implement `ParameterAttributeInterface` and the corresponding parameter attribute
resolver should implement `ParameterAttributeResolverInterface`.

### Example of custom parameter attribute

For example, let's create parameter attribute that provide random value for object property.

Attribute:

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

If your attribute is simple and doesn't require dependencies, you can combine attribute and its resolver in a single class.
For example:

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
