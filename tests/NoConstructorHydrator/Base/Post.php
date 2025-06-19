<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\NoConstructorHydrator\Base;

final class Post
{
    public int $id;
    public string $title = '';

    public function __construct(string $title = 'no-title')
    {
        $this->title = $title;
    }
}
