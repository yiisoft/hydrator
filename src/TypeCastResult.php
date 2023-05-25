<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

final class TypeCastResult
{
    private function __construct(
        private bool $isCasted,
        private mixed $value = null,
    ) {
    }

    public static function success(mixed $value): self
    {
        return new self(true, $value);
    }

    public static function skip(): self
    {
        return new self(false);
    }

    public function isCasted(): bool
    {
        return $this->isCasted;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
