<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe\CreatingOwnAttributes;

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
