<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeHandling\ResolverFactory;

use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;
use Yiisoft\Hydrator\AttributeHandling\Exception\AttributeResolverNonInstantiableException;

interface AttributeResolverFactoryInterface
{
    /**
     * @throws AttributeResolverNonInstantiableException
     */
    public function create(DataAttributeInterface|ParameterAttributeInterface $attribute): object;
}
