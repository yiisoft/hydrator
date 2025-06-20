<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Hydrator\NoConstructorBase;

use LogicException;

final class Post
{
    public int $id;
    public string $title = '';

    public function __construct()
    {
        throw new LogicException('Constructor should not be called.');
    }
}
