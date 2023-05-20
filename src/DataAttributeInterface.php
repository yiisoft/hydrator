<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * DataAttributeInterface is an interface for data attributes.
 */
interface DataAttributeInterface
{
    /**
     * Get resolver for the attribute.
     *
     * @return DataAttributeResolverInterface|string Resolver for the attribute.
     */
    public function getResolver(): string|DataAttributeResolverInterface;
}
