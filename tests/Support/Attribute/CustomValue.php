<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Attribute;
use Yiisoft\Hydrator\AttributeInfrastructure\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\AttributeInfrastructure\ParameterAttributeInterface;
use Yiisoft\Hydrator\AttributeInfrastructure\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Result;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class CustomValue implements ParameterAttributeInterface, ParameterAttributeResolverInterface
{
    public function __construct(
        private mixed $value,
    ) {
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, ParameterAttributeResolveContext $context): Result
    {
        return Result::success($this->value);
    }

    public function getResolver(): self
    {
        return $this;
    }
}
