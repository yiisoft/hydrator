<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Yiisoft\Hydrator\Internal\DataExtractor;

use function array_key_exists;

/**
 * Holds data to hydrate an object from and a map to use when populating an object.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
final class Data
{
    /**
     * @param array $data Data to hydrate object from.
     * @param array $map Object property names mapped to keys in the data array that hydrator will use when hydrating
     * an object.
     * @param bool $strict Whether to hydrate properties from the map only.
     *
     * @psalm-param MapType $map
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
     * @return array Data array to hydrate object from.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get object property names mapped to keys in the data array that hydrator will use when hydrating an object.
     *
     * @return array Object property names mapped to keys in the data array.
     *
     * @psalm-return MapType
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * Whether to hydrate properties from the map only.
     *
     * @return bool Whether to hydrate properties from the map only.
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Set data array to hydrate object from.
     *
     * @param array $data Data array to hydrate object from.
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Set object property names mapped to keys in the data array that hydrator will use when hydrating an object.
     *
     * @param array $map Object property names mapped to keys in the data array.
     *
     * @psalm-param MapType $map
     */
    public function setMap(array $map): void
    {
        $this->map = $map;
    }

    /**
     * Set whether to hydrate properties from the map only.
     *
     * @param bool $strict Whether to hydrate properties from the map only.
     */
    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
    }

    public function resolveValue(string $name): Result
    {
        if ($this->isStrict() && !array_key_exists($name, $this->map)) {
            return Result::fail();
        }

        return DataExtractor::getValueByPath($this->getData(), $this->map[$name] ?? $name);
    }
}
