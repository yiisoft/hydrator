<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * Creates or populates objects from a set of raw data.
 *
 * @psalm-type MapType = array<string,string|list<string>>
 */
interface HydratorInterface
{
    /**
     * Populates an object with data.
     *
     * @param object $object Object to populate.
     * @param array $data Data to populate an object with.
     * @param array $map Object property names mapped to keys in the data array that hydrator will use when populating an object.
     * @psalm-param MapType $map
     * @param bool $strict Whether to throw an exception if a data key isn't found in the map.
     */
    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void;

    /**
     * Creates an object and populates it with data.
     *
     * @psalm-template T
     *
     * @param string $class Class name to create.
     * @psalm-param class-string<T> $class
     * @param array $data Data to populate an object with.
     * @param array $map Object property names mapped to keys in the data array that hydrator will use when populating an object.
     * @psalm-param MapType $map
     * @param bool $strict Whether to throw an exception if a data key isn't found in the map.
     *
     * @return object Created and populated object.
     * @psalm-return T
     */
    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object;
}
