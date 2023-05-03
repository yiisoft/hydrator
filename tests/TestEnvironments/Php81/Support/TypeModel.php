<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support;

use Stringable;

final class TypeModel
{
    public int|string $intString = -1;
    public CarInterface&Stringable $intersection;

    public function __construct()
    {
        $this->intersection = new StringableCar('red');
    }
}
