<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe\CreatingOwnAttributes;

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
