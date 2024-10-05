<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ObjectMap;

use Yiisoft\Hydrator\Attribute\Parameter\Data;

final class Nested2
{
    public string $var1 = '';
    #[Data('var2')]
    public string $var2 = '';
}
