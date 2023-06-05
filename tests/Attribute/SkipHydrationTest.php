<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Classes\SkipHydrationClass;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class SkipHydrationTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator();

        $object = $hydrator->create(SkipHydrationClass::class, ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]);

        $this->assertNull($object->a);
        $this->assertSame(2, $object->b);
        $this->assertNull($object->c);
        $this->assertSame(104, $object->d);
        $this->assertSame(5, $object->e);
    }
}
