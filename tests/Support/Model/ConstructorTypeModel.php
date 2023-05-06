<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

final class ConstructorTypeModel
{
    public function __construct(
        public int $int = -1,
    ) {
    }
}
