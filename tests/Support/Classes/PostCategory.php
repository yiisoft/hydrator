<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Attribute\Parameter\Collection;

final class PostCategory
{
    public function __construct(
        #[Collection(Post::class)]
        private array $posts = [],
    ) {
    }

    public function getPosts(): array
    {
        return $this->posts;
    }
}
