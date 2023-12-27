<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php82\Support;

final readonly class ReadonlyClass
{
    public function __construct(
        public string $name,
        public int $age,
    ) {
    }
}
