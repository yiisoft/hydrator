<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe\CreatingOwnAttributes;

use Attribute;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final class FromArray implements DataAttributeInterface
{
    public function __construct(
        private array $data,
    ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getResolver(): string
    {
        return FromArrayResolver::class;
    }
}
