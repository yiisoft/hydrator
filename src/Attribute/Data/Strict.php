<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Data;

use Attribute;
use Yiisoft\Hydrator\Data;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;

#[Attribute(Attribute::TARGET_CLASS)]
final class Strict implements DataAttributeInterface, DataAttributeResolverInterface
{
    public function __construct(
        private bool $strict = true,
    ) {
    }

    public function getResolver(): self
    {
        return $this;
    }

    public function prepareData(DataAttributeInterface $attribute, Data $data): void
    {
        if (!$attribute instanceof self) {
            throw new UnexpectedAttributeException(self::class, $attribute);
        }

        $data->setStrict($this->strict);
    }
}
