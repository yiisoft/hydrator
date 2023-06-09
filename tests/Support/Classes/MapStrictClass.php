<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Attribute\Data\Map;

#[Map(['a' => 'a', 'b' => 'y'], true)]
final class MapStrictClass
{
    public string $a = '.';
    public string $b = '.';
    public string $c = '.';
}
