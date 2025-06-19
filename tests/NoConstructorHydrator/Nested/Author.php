<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\NoConstructorHydrator\Nested;

use LogicException;

final class Author
{
    public string $name = '';
    public int $age = 0;

    public function __construct()
    {
        throw new LogicException('Constructor should not be called.');
    }
}
