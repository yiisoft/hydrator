<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support;

final class ReadonlyClass
{
    public readonly int $a;
    public int $b;

    public function __construct(
        public readonly int $c = 0,
        public int $d = 0,
    ) {
        $this->a = 99;
    }
}
