<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

interface ParameterAttributeResolverInterface
{
    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): Result;
}
