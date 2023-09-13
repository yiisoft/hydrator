<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverFactory;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;

interface AttributeResolverFactoryInterface
{
    /**
     * @psalm-return ($attribute is DataAttributeInterface ? DataAttributeResolverInterface : ParameterAttributeResolverInterface)
     * @return object
     */
    public function create(DataAttributeInterface|ParameterAttributeInterface $attribute): object;
}
