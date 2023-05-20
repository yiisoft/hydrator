<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * A data attribute is an attribute that can be used to change the way data to be assigned is obtained.
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
