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
            'expectedValue' => [],
            'value' => [],
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [0],
            'value' => '',
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [42],
            'value' => 42,
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [42],
            'value' => '42',
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [42],
            'value' => [42],
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [1, 2, 3],
            'value' => [1, 2, 3],
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [1, 2, 3],
            'value' => ['1', '2', '3'],
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [1, 42, 1, 2],
            'value' => ['1', 42, true, 2.4],
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [1, 2, 3],
            'value' => new ArrayObject([1, 2, 3]),
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [1, 0, 2],
            'value' => [1, false, 2],
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [1, 0, 2],
            'value' => [1, null, 2],
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield [
            'expectedValue' => [10, 20, 30],
            'value' => ['10.5', '20.9', '30.1'],
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        // Test splitting with default separator (comma)
        yield [
            'expectedValue' => [1, 2, 3],
            'value' => '1,2,3',
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        // Test splitting with spaces
        yield [
            'expectedValue' => [1, 2, 3],
            'value' => '1, 2, 3',
            'object' => new class () {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        // Test custom separator
        yield [
            'expectedValue' => [1, 2, 3],
            'value' => '1;2;3',
            'object' => new class () {
                #[ToArrayOfIntegers(separator: ';')]
                public ?array $value = null;
            },
        ];
        // Test splitResolvedValue = false
        yield [
            'expectedValue' => [123],
            'value' => '1,2,3',
            'object' => new class () {
                #[ToArrayOfIntegers(splitResolvedValue: false)]
                public ?array $value = null;
            },
        ];
        // Test split with mixed types
        yield [
            'expectedValue' => [1, 2, 3, 4],
            'value' => '1,2.5,3,4.9',
            'object' => new class () {
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
