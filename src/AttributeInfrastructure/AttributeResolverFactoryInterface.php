<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeInfrastructure;

use Yiisoft\Hydrator\NonInstantiableException;

interface AttributeResolverFactoryInterface
{
    /**
     * @throws NonInstantiableException
     */
    public function create(DataAttributeInterface|ParameterAttributeInterface $attribute): object;
}
