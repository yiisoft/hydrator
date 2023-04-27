<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

use Yiisoft\Hydrator\Attribute\Data\Map;
use Yiisoft\Hydrator\Tests\Support\CustomData;

#[CustomData([
    'x' => 1,
    'y' => 2,
])]
#[Map([
    'a' => 'x',
    'b' => 'y',
])]
final class MapModel
{
    public string $a;
    public string $b;
}
