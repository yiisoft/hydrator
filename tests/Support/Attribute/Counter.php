<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Attribute;
use Yiisoft\Hydrator\ParameterAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Counter implements ParameterAttributeInterface
{
    public function __construct(
        private string $key
    ) {}

    public function getKey(): string
    {
        return $this->key;
    }

    public function getResolver(): string
    {
        return CounterResolver::class;
    }
}
