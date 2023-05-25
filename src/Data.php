<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * Holds data to hydrate an object from and a map to use when populating an object.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
final class Data
{
    /**
     * @param array $data Data to hydrate object from.
     * @param array $map Object property names mapped to keys in the data array that hydrator will use when populating an object.
     * @psalm-param MapType $map
     * @param bool $strict Whether to populate properties from the map only.
     */
    public function __construct(
        private array $data = [],
        private array $map = [],
        private bool $strict = false,
    ) {
    }

    /**
     * Get data to hydrate object from.
     *
     * @return array Data to hydrate object from.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get object property names mapped to keys in the data array that hydrator will use when populating an object.
     *
     * @return array Object property names mapped to keys in the data array.
     * @psalm-return MapType
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * Whether to throw an exception if a data key isn't found in the map.
     *
     * @return bool Whether to populate properties from the map only.
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Set data to hydrate object from.
     *
     * @param array $data Data to hydrate object from.
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Set object property names mapped to keys in the data array that hydrator will use when populating an object.
     *
     * @param array $map Object property names mapped to keys in the data array.
     * @psalm-param MapType $map
     */
    public function setMap(array $map): void
    {
        $this->map = $map;
    }

    /**
     * Set whether to populate properties from the map only.
     *
     * @param bool $strict Whether to populate properties from the map only.
     */
    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
    }
}
