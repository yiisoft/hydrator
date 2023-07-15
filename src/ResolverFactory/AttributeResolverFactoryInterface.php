<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverFactory;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;

interface AttributeResolverFactoryInterface
{
    /**
     * @psalm-template T
     * @psalm-param class-string<T>|T $resolver
     * @psalm-return T|object
     * @return object
     */
    public function create(DataAttributeInterface|ParameterAttributeInterface $attribute): object;
}
