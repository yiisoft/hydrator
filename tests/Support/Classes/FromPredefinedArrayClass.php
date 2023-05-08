<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArray;

#[FromPredefinedArray]
final class FromPredefinedArrayClass
{
    public string $a = '.';
    public string $b = '.';
}
