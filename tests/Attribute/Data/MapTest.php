<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Data;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Attribute\Data\Map;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArray;
use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArrayResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\FromPredefinedArrayClass;
use Yiisoft\Hydrator\Tests\Support\Classes\MapClass;
use Yiisoft\Hydrator\Tests\Support\Classes\MapNonStrictClass;
use Yiisoft\Hydrator\Tests\Support\Classes\MapStrictClass;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class MapTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = $hydrator->create(MapClass::class);

        $this->assertSame('1', $object->a);
        $this->assertSame('2', $object->b);
    }

    public function testStrict(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = $hydrator->create(MapStrictClass::class);

        $this->assertSame('1', $object->a);
        $this->assertSame('2', $object->b);
        $this->assertSame('.', $object->c);
    }

    public function testNonStrict(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = $hydrator->create(MapNonStrictClass::class, strict: true);

        $this->assertSame('1', $object->a);
        $this->assertSame('2', $object->b);
        $this->assertSame('3', $object->c);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            new SimpleContainer([FromPredefinedArrayResolver::class => new Map([])])
        );

        $object = new FromPredefinedArrayClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . Map::class . '", but "' . FromPredefinedArray::class . '" given.');
        $hydrator->hydrate($object);
    }
}
