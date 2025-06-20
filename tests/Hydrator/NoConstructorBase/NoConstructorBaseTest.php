<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Hydrator\NoConstructorBase;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

final class NoConstructorBaseTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator();

        $post = $hydrator->create(Post::class, ['id' => 7], false);

        assertInstanceOf(Post::class, $post);
        assertSame(7, $post->id);
        assertSame('', $post->title);
    }
}
