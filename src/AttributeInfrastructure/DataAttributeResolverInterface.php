<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeInfrastructure;

use Yiisoft\Hydrator\Data;

/**
 * An interface for resolvers of attributes that implement {@see DataAttributeInterface}.
 */
interface DataAttributeResolverInterface
{
    /**
     * Prepare {@see Data} object that used for hydration.
     *
     * @param DataAttributeInterface $attribute The attribute to be resolved.
     * @param Data $data Current {@see Data} object.
     */
    public function prepareData(DataAttributeInterface $attribute, Data $data): void;
}
