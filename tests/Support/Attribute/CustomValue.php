<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Attribute;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Result;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class CustomValue implements ParameterAttributeInterface, ParameterAttributeResolverInterface
{
    public function __construct(
        private mixed $value,
    ) {
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): Result
    {
        return Result::success($this->value);
    }

    public function getResolver(): self
    {
        return $this;
    }
}
