<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class ToArrayOfStrings implements ParameterAttributeInterface
{
    public function __construct(
        public readonly bool $trim = false,
        public readonly bool $skipEmpty = false,
        public readonly bool $splitResolvedStringValue = true,
    ) {
    }

    public function getResolver(): string
    {
        return ToArrayOfStringsResolver::class;
    }
}
