<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ObjectMap;

final class Car
{
    public function __construct(
        public ?Engine $engine = null,
    ) {
    }
}
