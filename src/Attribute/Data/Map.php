<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Data;

use Attribute;
use Yiisoft\Hydrator\Data;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final class Map implements DataAttributeInterface, DataAttributeResolverInterface
{
    public function __construct(
        private array $map
    ) {}

    public function getResolver(): self
    {
        return $this;
    }

    public function prepareData(DataAttributeInterface $attribute, Data $data): void
    {
        $data->setMap($this->map);
    }
}
