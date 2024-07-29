<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\Chart;

use Yiisoft\Hydrator\Attribute\Parameter\Collection;

final class Chart
{
    public function __construct(
        #[Collection(Point::class)]
        private array $points = [],
        // private ?Point $point = null,
    )
    {
    }

    public function getPoints(): array
    {
        return $this->points;
    }

//    public function getPoint(): ?Point
//    {
//        return $this->point;
//    }
}
