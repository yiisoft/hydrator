<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

final class PreparePropertyClass
{
    public function __construct(
        private string $a,
    ) {
        $this->a .= '!';
    }

    public function getA(): string
    {
        return $this->a;
    }
}
