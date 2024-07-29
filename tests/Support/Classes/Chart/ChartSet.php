<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\Chart;

use Yiisoft\Hydrator\Attribute\Parameter\Collection;

final class ChartSet
{
    public function __construct(
        #[Collection(Chart::class)]
        private array $charts = [],
    )
    {
    }

    public function getCharts(): array
    {
        return $this->charts;
    }
}
