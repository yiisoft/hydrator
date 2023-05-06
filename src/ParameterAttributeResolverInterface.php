<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

interface ParameterAttributeResolverInterface
{
    /**
     * @throws NotResolvedException
     */
    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed;
}
