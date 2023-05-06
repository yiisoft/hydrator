<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

interface ParameterAttributeInterface
{
    /**
     * @return ParameterAttributeResolverInterface|string
     */
    public function getResolver(): string|ParameterAttributeResolverInterface;
}
