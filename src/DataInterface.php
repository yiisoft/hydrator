<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * @psalm-import-type MapType from HydratorInterface
 */
interface DataInterface
{
    public function getData(): array;

    /**
     * @psalm-return MapType
     */
    public function getMap(): array;

    public function isStrict(): bool;

    public function setData(array $data): void;

    /**
     * @psalm-param MapType $map
     */
    public function setMap(array $map): void;

    public function setStrict(bool $strict): void;
}
