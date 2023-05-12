<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use Attribute;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\DataInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final class CustomData implements DataAttributeInterface, DataAttributeResolverInterface
{
    public function __construct(
        private array $data
    ) {
    }

    public function getResolver(): self
    {
        return $this;
    }

    public function prepareData(DataAttributeInterface $attribute, DataInterface $data): void
    {
        $data->setData($this->data);
    }
}
