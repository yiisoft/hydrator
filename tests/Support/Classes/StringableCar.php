<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Stringable;

final class StringableCar implements CarInterface, Stringable
{
    public function __construct(
        private string $color,
    ) {
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function __toString(): string
    {
        return $this->color . ' car';
    }
}
