<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Data;

use Attribute;
use Yiisoft\Hydrator\DataInterface;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;

/**
 * @psalm-import-type MapType from HydratorInterface
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Map implements DataAttributeInterface, DataAttributeResolverInterface
{
    /**
     * @psalm-param MapType $map
     */
    public function __construct(
        private array $map
    ) {
    }

    public function getResolver(): self
    {
        return $this;
    }

    public function prepareData(DataAttributeInterface $attribute, DataInterface $data): void
    {
        if (!$attribute instanceof self) {
            throw new UnexpectedAttributeException(self::class, $attribute);
        }

        $data->setMap($this->map);
    }
}
