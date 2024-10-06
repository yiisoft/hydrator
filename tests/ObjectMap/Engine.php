<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ObjectMap;

final class Engine
{
    public string $version = '';

    public function __construct(
        public string $name,
    ) {
    }
}
