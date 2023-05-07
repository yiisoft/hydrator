<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Object;

final class PreparePropertyObject
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
