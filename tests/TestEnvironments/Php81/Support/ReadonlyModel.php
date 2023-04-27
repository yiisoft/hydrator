<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support;

final class ReadonlyModel
{
    public readonly int $a;

    public function __construct(
        public readonly int $b = 0,
        public int $c = 0,
    )
    {
        $this->a = 99;
    }
}

