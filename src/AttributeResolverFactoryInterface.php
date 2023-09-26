<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\Exception\NonInstantiableException;
use Yiisoft\Hydrator\ParameterAttributeInterface;

interface AttributeResolverFactoryInterface
{
    /**
     * @throws NonInstantiableException
     */
    public function create(DataAttributeInterface|ParameterAttributeInterface $attribute): object;
}
