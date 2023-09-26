<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Attribute;
use Yiisoft\Hydrator\AttributeInfrastructure\DataAttributeInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final class InvalidDataResolver implements DataAttributeInterface
{
    public function getResolver(): string
    {
        return NotResolver::class;
    }
}
