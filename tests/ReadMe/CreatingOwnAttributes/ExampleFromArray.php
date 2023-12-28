<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe\CreatingOwnAttributes;

#[FromArray(['a' => 1, 'b' => 2])]
final class ExampleFromArray
{
    public int $a = 0;
    public int $b = 0;
}
