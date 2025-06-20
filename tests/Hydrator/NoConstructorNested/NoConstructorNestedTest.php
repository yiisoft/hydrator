<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Hydrator\NoConstructorNested;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

final class NoConstructorNestedTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator();

        $post = $hydrator->create(
            Post::class,
            [
                'title' => 'Test',
                'author' => [
                    'name' => 'John Doe',
                    'age' => 30,
                ],
            ],
            false,
        );

        assertInstanceOf(Post::class, $post);
        assertSame('Test', $post->title);
        assertInstanceOf(Author::class, $post->author);
        assertSame('John Doe', $post->author->name);
        assertSame(30, $post->author->age);
    }
}
