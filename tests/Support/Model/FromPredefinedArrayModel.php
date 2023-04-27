<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArray;

#[FromPredefinedArray]
final class FromPredefinedArrayModel
{
    public string $a = '.';
    public string $b = '.';
}
