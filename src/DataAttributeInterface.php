<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

interface DataAttributeInterface
{
    /**
     * @return DataAttributeResolverInterface|string
     */
    public function getResolver(): string|DataAttributeResolverInterface;
}
