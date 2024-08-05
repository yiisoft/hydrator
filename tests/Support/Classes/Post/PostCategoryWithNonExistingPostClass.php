<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\Post;

use Yiisoft\Hydrator\Attribute\Parameter\Collection;

final class PostCategoryWithNonExistingPostClass
{
    public function __construct(
        #[Collection('NonExistingPostClass')]
        private array $posts = [],
    ) {
    }

    public function getPosts(): array
    {
        return $this->posts;
    }
}
