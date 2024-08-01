<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\Chart;

final class Point
{
    public function __construct(
        private Coordinates $coordinates,
        private array $rgb,
    ) {
    }
}
