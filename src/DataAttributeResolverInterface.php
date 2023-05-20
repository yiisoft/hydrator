<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * DataAttributeResolverInterface is an interface for data attribute resolvers.
 */
interface DataAttributeResolverInterface
{
    /**
     * @param DataAttributeInterface $attribute Data attribute to resolve.
     * @param Data $data Data to resolve attribute for.
     * @return void
     */
    public function prepareData(DataAttributeInterface $attribute, Data $data): void;
}
