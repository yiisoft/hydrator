<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeResolverInterface;

final class CustomResolverAttr implements ParameterAttributeInterface
{
    public function __construct(
        private string|ParameterAttributeResolverInterface $resolver
    ) {
    }

    public function getResolver(): string|ParameterAttributeResolverInterface
    {
        return $this->resolver;
    }
}
