<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ObjectMap;

final class Nested
{
    public string $var = '';
    public ?Nested2 $nested2 = null;
}
