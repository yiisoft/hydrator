<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Attribute;

/**
 * Casts the resolved value to array of integers.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class ToArrayOfIntegers implements ParameterAttributeInterface
{
    public function getResolver(): string
    {
        return ToArrayOfIntegersResolver::class;
    }
}
