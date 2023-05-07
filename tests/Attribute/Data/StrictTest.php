<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Data;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Attribute\Data\Strict;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArray;
use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArrayResolver;
use Yiisoft\Hydrator\Tests\Support\Object\FromPredefinedArrayObject;
use Yiisoft\Hydrator\Tests\Support\Object\StrictObject;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class StrictTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = $hydrator->create(StrictObject::class);

        $this->assertSame('1', $object->a);
        $this->assertSame('2', $object->b);
        $this->assertSame('.', $object->c);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            new SimpleContainer([FromPredefinedArrayResolver::class => new Strict()])
        );

        $object = new FromPredefinedArrayObject();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . Strict::class . '", but "' . FromPredefinedArray::class . '" given.');
        $hydrator->hydrate($object);
    }
}
