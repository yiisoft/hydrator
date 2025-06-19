<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\NoConstructorHydrator\Nested;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\NoConstructorHydrator;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

final class NoConstructorNestedTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new NoConstructorHydrator();

        $post = $hydrator->create(
            Post::class,
            [
                'title' => 'Test',
                'author' => [
                    'name' => 'John Doe',
                    'age' => 30,
                ],
            ],
        );

        assertInstanceOf(Post::class, $post);
        assertSame('Test', $post->title);
        assertInstanceOf(Author::class, $post->author);
        assertSame('John Doe', $post->author->name);
        assertSame(30, $post->author->age);
    }
}
