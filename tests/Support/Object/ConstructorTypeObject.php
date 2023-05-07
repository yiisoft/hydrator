<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Object;

final class ConstructorTypeObject
{
    public function __construct(
        public int $int = -1,
    ) {
    }
}
