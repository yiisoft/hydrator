<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * `ParameterAttributeInterface` is an interface for parameter attributes.
 */
interface ParameterAttributeResolverInterface
{
    /**
     * Returns the parameter value for the attribute specified.
     *
     * @param ParameterAttributeInterface $attribute The attribute to be resolved.
     * @param Context $context The context of the attribute.
     *
     * @return Result The parameter value for the attribute specified.
     */
    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): Result;
}
