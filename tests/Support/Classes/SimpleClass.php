<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

final class SimpleClass
{
    private string $c;

    public function __construct(
        private string $a = '.',
        private string $b = '.',
        string $c = '.',
    ) {
        $this->c = $c;
    }

    public function getA(): string
    {
        return $this->a;
    }

    public function getB(): string
    {
        return $this->b;
    }

    public function getC(): string
    {
        return $this->c;
    }
}
