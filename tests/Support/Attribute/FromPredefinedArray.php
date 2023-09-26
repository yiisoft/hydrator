<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Attribute;
use Yiisoft\Hydrator\AttributeInfrastructure\DataAttributeInterface;
use Yiisoft\Hydrator\AttributeInfrastructure\ParameterAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
final class FromPredefinedArray implements ParameterAttributeInterface, DataAttributeInterface
{
    public function __construct(
        private ?string $key = null
    ) {
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getResolver(): string
    {
        return FromPredefinedArrayResolver::class;
    }
}
