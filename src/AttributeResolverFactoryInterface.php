<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Yiisoft\Hydrator\Exception\NonInstantiableException;

interface AttributeResolverFactoryInterface
{
    /**
     * @throws NonInstantiableException
     */
    public function create(DataAttributeInterface|ParameterAttributeInterface $attribute): object;
}
