<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

use Yiisoft\Hydrator\Attribute\Data\Map;
use Yiisoft\Hydrator\Attribute\Data\Strict;
use Yiisoft\Hydrator\Tests\Support\CustomData;

#[CustomData(['a' => 1, 'y' => 2, 'c' => 3])]
#[Map(['a' => 'a', 'b' => 'y'])]
#[Strict]
final class StrictModel
{
    public string $a = '.';
    public string $b = '.';
    public string $c = '.';
}
