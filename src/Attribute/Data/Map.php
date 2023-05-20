<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Data;

use Attribute;
use Yiisoft\Hydrator\Data;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;

/**
 * Maps object property names to keys in the data array.
 * When a class has the attribute, hydrator fills object properties
 * according to the map instead of using same-named data keys.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Map implements DataAttributeInterface, DataAttributeResolverInterface
{
    /**
     * @psalm-param MapType $map
     */
    public function __construct(
        private array $map,
        private ?bool $strict = null,
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

        $data->setMap($this->map);

        if ($this->strict !== null) {
            $data->setStrict($this->strict);
        }
    }
}
