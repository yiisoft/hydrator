<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

final class ConstructorTypeClass
{
    public function __construct(
        public int $int = -1,
    ) {
    }
}
