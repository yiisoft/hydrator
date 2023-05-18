<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\NotResolvedException;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;

final class ContextViewerResolver implements ParameterAttributeResolverInterface
{
    private ?Context $context;

    public function getContext(): ?Context
    {
        return $this->context;
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed
    {
        $this->context = $context;
        throw new NotResolvedException();
    }
}
