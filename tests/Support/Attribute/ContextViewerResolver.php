<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Yiisoft\Hydrator\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Result;

final class ContextViewerResolver implements ParameterAttributeResolverInterface
{
    private ?ParameterAttributeResolveContext $context = null;

    public function getContext(): ?ParameterAttributeResolveContext
    {
        return $this->context;
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, ParameterAttributeResolveContext $context): Result
    {
        $this->context = $context;
        return Result::fail();
    }
}
