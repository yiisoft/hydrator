<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Yiisoft\Hydrator\Exception\NonInstantiableException;

/**
 * Creates or hydrate objects from a set of raw data.
 *
 * @psalm-type MapType = array<string,string|list<string>>
 */
interface HydratorInterface
{
    /**
     * Hydrates an object with data.
     *
     * @param object $object Object to hydrate.
     * @param array $data Data array to hydrate an object with.
     * @param array $map Object property names mapped to keys in the data array that hydrator will use when hydrating
     * an object.
     * @param bool $strict Whether to hydrate properties from the map only.
     *
     * @psalm-param MapType $map
     */
    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void;

    /**
     * Creates an object and hydrates it with data.
     *
     * @param string $class The class name to create.
     * @param array $data Data array to hydrate an object with.
     * @param array $map Object property names mapped to keys in the data array that hydrator will use when hydrating
     * an object.
     * @param bool $strict Whether to hydrate properties from the map only.
     *
     * @throws NonInstantiableException
     * @return object Created and hydrated object.
     *
     * @psalm-template T
     * @psalm-param class-string<T> $class
     * @psalm-param MapType $map
     * @psalm-return T
     */
    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object;
}
