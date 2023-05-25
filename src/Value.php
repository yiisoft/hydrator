<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

final class Value
{
    private function __construct(
        private bool $exist,
        private mixed $value = null,
    ) {
    }

    public static function success(mixed $value): self
    {
        return new self(true, $value);
    }

    public static function fail(): self
    {
        return new self(false);
    }

    public function exist(): bool
    {
        return $this->exist;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
