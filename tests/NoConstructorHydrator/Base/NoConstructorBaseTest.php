<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\NoConstructorHydrator\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\NoConstructorHydrator;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

final class NoConstructorBaseTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new NoConstructorHydrator();

        $post = $hydrator->create(Post::class, ['id' => 7]);

        assertInstanceOf(Post::class, $post);
        assertSame(7, $post->id);
        assertSame('', $post->title);
    }
}
