<?php
declare(strict_types=1);

namespace Yiisoft\Hydrator;

interface ParameterAttributeInterface
{
    /**
     * @return string|ParameterAttributeResolverInterface
     */
    public function getResolver(): string|ParameterAttributeResolverInterface;
}
