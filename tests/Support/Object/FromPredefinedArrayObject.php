<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Object;

use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArray;

#[FromPredefinedArray]
final class FromPredefinedArrayObject
{
    public string $a = '.';
    public string $b = '.';
}
