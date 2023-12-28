<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe\CreatingOwnAttributes;

use Attribute;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class RandomInt implements ParameterAttributeInterface
{
    public function __construct(
        private int $min = 0,
        private int $max = 99,
    ) {
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getResolver(): string
    {
        return RandomIntResolver::class;
    }
}
