<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

/**
 * @psalm-import-type MapType from HydratorInterface
 */
final class Data
{
    /**
     * @psalm-param MapType $map
     */
    public function __construct(
        private array $data,
        private array $map,
        private bool $strict,
    ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @psalm-return MapType
     */
    public function getMap(): array
    {
        return $this->map;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @psalm-param MapType $map
     */
    public function setMap(array $map): void
    {
        $this->map = $map;
    }

    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
    }
}
