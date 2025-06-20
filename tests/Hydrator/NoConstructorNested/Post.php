<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Hydrator\NoConstructorNested;

use LogicException;

final class Post
{
    public string $title = '';
    public ?Author $author = null;

    public function __construct()
    {
        throw new LogicException('Constructor should not be called.');
    }
}
