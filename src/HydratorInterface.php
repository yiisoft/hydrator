<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * @psalm-type MapType = array<string,string|list<string>>
 */
interface HydratorInterface
{
    /**
     * @psalm-param MapType $map
     */
    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void;

    /**
     * @psalm-template T
     *
     * @psalm-param class-string<T> $class
     * @psalm-param MapType $map
     *
     * @psalm-return T
     */
    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object;
}
