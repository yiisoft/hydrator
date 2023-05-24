<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Attribute\Data\Map;
use Yiisoft\Hydrator\Tests\Support\CustomData;

#[CustomData(['a' => 1, 'y' => 2, 'c' => 3])]
#[Map(['b' => 'y'], false)]
final class MapNonStrictClass
{
    public string $a = '.';
    public string $b = '.';
    public string $c = '.';
}