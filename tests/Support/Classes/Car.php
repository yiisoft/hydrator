<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

final class Car
{
    public function __construct(
        public EngineInterface $engine,
    ) {
    }
}
