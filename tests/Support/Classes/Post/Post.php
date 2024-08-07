<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\Post;

final class Post
{
    public function __construct(
        private string $name,
        private string $description = '',
    ) {
    }
}
