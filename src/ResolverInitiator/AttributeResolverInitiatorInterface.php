<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverInitiator;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;

interface AttributeResolverInitiatorInterface
{
    /**
     * @psalm-template T
     * @psalm-param class-string<T>|T $resolver
     * @psalm-return T
     * @return object
     */
    public function initiate(DataAttributeInterface|ParameterAttributeInterface $attribute): object;
}
