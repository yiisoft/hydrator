<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe\CreatingOwnAttributes;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;

final class ExampleTest extends TestCase
{
    public function testFromArray(): void
    {
        $object = (new Hydrator())->create(ExampleFromArray::class);

        $this->assertSame(1, $object->a);
        $this->assertSame(2, $object->b);
    }

    public function testRandomInt(): void
    {
        $object = new class () {
            #[RandomInt(100, 200)]
            public int $a = -1;
            #[RandomInt(50, 80)]
            public int $b = -1;
        };

        (new Hydrator())->hydrate($object);

        $this->assertGreaterThanOrEqual(100, $object->a);
        $this->assertLessThanOrEqual(200, $object->a);
        $this->assertGreaterThanOrEqual(50, $object->b);
        $this->assertLessThanOrEqual(80, $object->b);
    }
}
