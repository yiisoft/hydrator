<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeResolverInterface;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\DataInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;

use function array_key_exists;
use function is_array;

final class FromPredefinedArrayResolver implements ParameterAttributeResolverInterface, DataAttributeResolverInterface
{
    private array $array = [];

    public function setArray(array $array): void
    {
        $this->array = $array;
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, ParameterAttributeResolveContext $context): Result
    {
        if (!$attribute instanceof FromPredefinedArray) {
            throw new UnexpectedAttributeException(FromPredefinedArray::class, $attribute);
        }

        $key = $attribute->getKey();

        if ($key === null) {
            return Result::success($this->array);
        }
        if (array_key_exists($key, $this->array)) {
            return Result::success($this->array[$key]);
        }

        return Result::fail();
    }

    public function prepareData(DataAttributeInterface $attribute, DataInterface $data): DataInterface
    {
        if (!$attribute instanceof FromPredefinedArray) {
            throw new UnexpectedAttributeException(FromPredefinedArray::class, $attribute);
        }

        $key = $attribute->getKey();
        if ($key === null) {
            return new ArrayData($this->array);
        }
        if (array_key_exists($key, $this->array) && is_array($this->array[$key])) {
            return new ArrayData($this->array[$key]);
        }

        return $data;
    }
}
