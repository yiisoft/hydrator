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
    /**
     * @param bool $splitResolvedValue Split non-array resolved value to array of strings by {@see $separator}
     * before casting each element to integer.
     * @param string $separator The boundary string. It is a part of regular expression
     * so should be taken into account or properly escaped with {@see preg_quote()}.
     */
    public function __construct(
        public readonly bool $splitResolvedValue = true,
        public readonly string $separator = ',',
    ) {}

    public function getResolver(): string
    {
        return ToArrayOfIntegersResolver::class;
    }
}
