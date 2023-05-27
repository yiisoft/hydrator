<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Result;

final class ContextViewerResolver implements ParameterAttributeResolverInterface
{
    private ?Context $context = null;

    public function getContext(): ?Context
    {
        return $this->context;
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): Result
    {
        $this->context = $context;
        return Result::fail();
    }
}
