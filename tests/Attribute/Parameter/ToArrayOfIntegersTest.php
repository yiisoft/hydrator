<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use ArrayObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Attribute\Parameter\ToArrayOfIntegers;
use Yiisoft\Hydrator\Attribute\Parameter\ToArrayOfIntegersResolver;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ToArrayOfIntegersTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield [
            [],
            [],
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [0],
            '',
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [42],
            42,
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [42],
            '42',
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [42],
            [42],
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [1, 2, 3],
            [1, 2, 3],
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [1, 2, 3],
            ['1', '2', '3'],
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [1, 42, 1, 2],
            ['1', 42, true, 2.4],
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [1, 2, 3],
            new ArrayObject([1, 2, 3]),
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [1, 0, 2],
            [1, false, 2],
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [1, 0, 2],
            [1, null, 2],
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            [10, 20, 30],
            ['10.5', '20.9', '30.1'],
            new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(mixed $expectedValue, mixed $value, object $object)
    {
        (new Hydrator())->hydrate($object, ['value' => $value]);
        $this->assertSame($expectedValue, $object->value);
    }

    public function testNotResolved(): void
    {
        $object = new class () {
            #[ToArrayOfIntegers]
            public ?array $value = null;
        };

        (new Hydrator())->hydrate($object);

        $this->assertNull($object->value);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    CounterResolver::class => new ToArrayOfIntegersResolver(),
                ]),
            ),
        );

        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . ToArrayOfIntegers::class . '", but "' . Counter::class . '" given.'
        );
        $hydrator->hydrate($object);
    }
}
