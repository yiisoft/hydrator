<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\Data;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Exception\UnexpectedAttributeException;

use function array_key_exists;

final class FromPredefinedArrayResolver implements ParameterAttributeResolverInterface, DataAttributeResolverInterface
{
    private array $array = [];

    public function setArray(array $array): void
    {
        $this->array = $array;
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): Result
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

    public function prepareData(DataAttributeInterface $attribute, Data $data): void
    {
        if (!$attribute instanceof FromPredefinedArray) {
            throw new UnexpectedAttributeException(FromPredefinedArray::class, $attribute);
        }

        $key = $attribute->getKey();
        if ($key === null) {
            $data->setData($this->array);
        } elseif (array_key_exists($key, $this->array) && is_array($this->array[$key])) {
            $data->setData($this->array[$key]);
        }
    }
}
