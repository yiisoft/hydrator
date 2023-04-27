<?php
declare(strict_types=1);

namespace Yiisoft\Hydrator;

interface DataAttributeInterface
{
    /**
     * @return string|DataAttributeResolverInterface
     */
    public function getResolver(): string|DataAttributeResolverInterface;
}
