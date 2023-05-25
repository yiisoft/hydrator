<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Attribute\Data\Map;

#[Map([
    'a' => 'x',
    'b' => 'y',
])]
final class MapClass
{
    public string $a;
    public string $b;
}
