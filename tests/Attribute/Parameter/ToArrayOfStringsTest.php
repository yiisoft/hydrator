<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use ArrayObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\Attribute\Parameter\ToArrayOfStrings;
use Yiisoft\Hydrator\Attribute\Parameter\ToArrayOfStringsResolver;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ToArrayOfStringsTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield [
            [],
            [],
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            [''],
            '',
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            [''],
            new stdClass(),
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello'],
            'hello',
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello'],
            ['hello'],
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello '],
            'hello ',
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello'],
            'hello ',
            new class() {
                #[ToArrayOfStrings(trim: true)]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 'world'],
            "hello\nworld",
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 'world'],
            ['hello', 'world'],
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', '42', '1', '2.4'],
            ['hello', 42, true, 2.4],
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 'world'],
            new ArrayObject(['hello', 'world']),
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ["hello\nworld"],
            "hello\nworld",
            new class() {
                #[ToArrayOfStrings(splitResolvedValue: false)]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', '', 'world'],
            "hello\n\nworld",
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 2 => 'world'],
            "hello\n\nworld",
            new class() {
                #[ToArrayOfStrings(skipEmpty: true)]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', '', ' world', ' good '],
            "hello\n\n world\n good ",
            new class() {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 2 => 'world', 3 => 'good'],
            "hello\n\n world\n good ",
            new class() {
                #[ToArrayOfStrings(trim: true, skipEmpty: true)]
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
            #[ToArrayOfStrings]
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
                    CounterResolver::class => new ToArrayOfStringsResolver(),
                ]),
            ),
        );

        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . ToArrayOfStrings::class . '", but "' . Counter::class . '" given.'
        );
        $hydrator->hydrate($object);
    }
}
