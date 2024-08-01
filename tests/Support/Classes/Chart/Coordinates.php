<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\Chart;

final class Coordinates
{
    public function __construct(
        private int $x,
        private int $y,
    ) {
    }
}
