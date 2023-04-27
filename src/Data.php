<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

final class Data
{
    public function __construct(
        private array $data,
        private array $map,
        private bool $strict,
    ) {}

    public function getData(): array
    {
        return $this->data;
    }

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

    public function setMap(array $map): void
    {
        $this->map = $map;
    }

    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
    }
}
