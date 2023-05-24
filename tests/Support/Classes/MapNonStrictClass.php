<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Attribute\Data\Map;

#[Map(['b' => 'y'], false)]
final class MapNonStrictClass
{
    public string $a = '.';
    public string $b = '.';
    public string $c = '.';
}
