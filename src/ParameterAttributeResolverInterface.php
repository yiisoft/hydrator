<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * An interface for resolvers of attributes that implement {@see ParameterAttributeInterface}.
 */
interface ParameterAttributeResolverInterface
{
    /**
     * Returns the resolved from specified attribute value object.
     *
     * @param ParameterAttributeInterface $attribute The attribute to be resolved.
     * @param Context $context The context of value resolving from attribute.
     *
     * @return Result The resolved from specified attribute value object.
     */
    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): Result;
}
