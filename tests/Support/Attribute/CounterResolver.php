<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\UnexpectedAttributeException;

final class CounterResolver implements ParameterAttributeResolverInterface
{
    private array $data = [];

    public function getCount(string $key): int
    {
        return $this->data[$key] ?? 0;
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): Result
    {
        if (!$attribute instanceof Counter) {
            throw new UnexpectedAttributeException(Counter::class, $attribute);
        }

        $key = $attribute->getKey();

        if (!isset($this->data[$key])) {
            $this->data[$key] = 0;
        }

        $this->data[$key]++;

        return Result::fail();
    }
}
