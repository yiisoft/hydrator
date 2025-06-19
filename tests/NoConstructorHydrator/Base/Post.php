<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\NoConstructorHydrator\Base;

final class Post
{
    public int $id;

    public function __construct(public string $title = 'no-title')
    {
    }
}
