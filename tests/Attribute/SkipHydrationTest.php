<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Object\SkipHydrationObject;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class SkipHydrationTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = $hydrator->create(SkipHydrationObject::class, ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);

        $this->assertNull($object->a);
        $this->assertSame(2, $object->b);
        $this->assertNull($object->c);
        $this->assertSame(4, $object->d);
    }
}
