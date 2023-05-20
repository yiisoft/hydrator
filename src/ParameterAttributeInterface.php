<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * ParameterAttributeInterface is an interface for parameter attributes.
 */
interface ParameterAttributeInterface
{
    /**
     * Returns resolver for the attribute.
     *
     * @return ParameterAttributeResolverInterface|string Resolver for the attribute.
     */
    public function getResolver(): string|ParameterAttributeResolverInterface;
}
