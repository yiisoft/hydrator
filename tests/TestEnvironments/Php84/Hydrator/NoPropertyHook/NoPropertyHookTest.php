<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php84\Hydrator\NoPropertyHook;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;

use function PHPUnit\Framework\assertSame;

final class NoPropertyHookTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator();
        $object = $hydrator->create(Book::class, ['title' => 'test']);

        assertSame('test', $object->title);
    }
}
