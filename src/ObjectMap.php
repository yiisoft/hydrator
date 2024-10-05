<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use function array_key_exists;

/**
 * Class provides mapping object property names to keys in the data array.
 *
 * @psalm-import-type MapType from ArrayData
 */
final class ObjectMap
{
    /**
     * @param array $map Object property names mapped to keys in the data array that hydrator will use when hydrating
     *  an object.
     * @psalm-param MapType $map
     */
    public function __construct(
        public readonly array $map
    ) {
    }

    /**
     * @psalm-return string|list<string>|ObjectMap|null
     */
    public function getPath(string $name): string|array|ObjectMap|null
    {
        return $this->map[$name] ?? null;
    }

    /**
     * @return string[]
     * @psalm-return list<string>
     */
    public function getNames(): array
    {
        return array_keys($this->map);
    }

    public function exists(string $name): bool
    {
        return array_key_exists($name, $this->map);
    }
}
