<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * Data attribute allows preparing data for the attribute.
 */
interface DataAttributeResolverInterface
{
    /**
     * Prepare data for the attribute.
     *
     * @param DataAttributeInterface $attribute Data attribute to resolve.
     * @param Data $data Data to resolve attribute for.
     */
    public function prepareData(DataAttributeInterface $attribute, Data $data): void;
}
