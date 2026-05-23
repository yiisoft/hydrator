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
        yield 'empty array' => [
            'expectedValue' => [],
            'value' => [],
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'empty string' => [
            'expectedValue' => [],
            'value' => '',
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'integer' => [
            'expectedValue' => [42],
            'value' => 42,
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'numeric string' => [
            'expectedValue' => [42],
            'value' => '42',
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'array of integers' => [
            'expectedValue' => [1, 2, 3],
            'value' => [1, 2, 3],
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'array of numeric strings' => [
            'expectedValue' => [1, 2, 3],
            'value' => ['1', '2', '3'],
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'array of mixed types' => [
            'expectedValue' => [1, 42, 1, 2],
            'value' => ['1', 42, true, 2.4],
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'array with empty strings' => [
            'expectedValue' => [1, 0],
            'value' => ['1', ''],
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'SPL array object' => [
            'expectedValue' => [1, 2, 3],
            'value' => new ArrayObject([1, 2, 3]),
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'array of mixed types with false' => [
            'expectedValue' => [1, 0, 2],
            'value' => [1, false, 2],
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'array of mixed types with null' => [
            'expectedValue' => [1, 0, 2],
            'value' => [1, null, 2],
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'array of mixed types with float' => [
            'expectedValue' => [10, 20, 30],
            'value' => ['10.5', '20.9', '30.1'],
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'splitting with default separator (comma)' => [
            'expectedValue' => [1, 2, 3],
            'value' => '1,2,3',
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'splitting with empty values' => [
            'expectedValue' => [1, 0, 2],
            'value' => '1,   ,2',
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'splitting with spaces' => [
            'expectedValue' => [1, 2, 3],
            'value' => '1, 2, 3',
            'object' => new class {
                #[ToArrayOfIntegers]
                public ?array $value = null;
            },
        ];
        yield 'custom separator' => [
            'expectedValue' => [1, 2, 3],
            'value' => '1;2;3',
            'object' => new class {
                #[ToArrayOfIntegers(separator: ';')]
                public ?array $value = null;
            },
        ];
        yield 'splitResolvedValue = false' => [
            'expectedValue' => [1],
            'value' => '1,2,3',
            'object' => new class {
                #[ToArrayOfIntegers(splitResolvedValue: false)]
                public ?array $value = null;
            },
        ];
        yield 'splitResolvedValue = false with empty value' => [
            'expectedValue' => [],
            'value' => '',
            'object' => new class {
                #[ToArrayOfIntegers(splitResolvedValue: false)]
                public ?array $value = null;
            },
        ];
        yield 'split with mixed types' => [
            'expectedValue' => [1, 2, 3, 4],
            'value' => '1,2.5,3,4.9',
            'object' => new class {
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
        $object = new class {
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
            'Expected "' . ToArrayOfIntegers::class . '", but "' . Counter::class . '" given.',
        );
        $hydrator->hydrate($object);
    }
}
