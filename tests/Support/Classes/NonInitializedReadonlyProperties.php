<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

final class NonInitializedReadonlyProperties
{
    public readonly string $a;

    public function __construct(
        public readonly string $b,
    ) {
    }
}
