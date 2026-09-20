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
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            [''],
            '',
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            [''],
            new stdClass(),
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello'],
            'hello',
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello'],
            ['hello'],
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello '],
            'hello ',
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello'],
            'hello ',
            new class {
                #[ToArrayOfStrings(trim: true)]
                public ?array $value = null;
            },
        ];
        yield [
            ["hello\u{2003}"],
            " hello\u{2003} ",
            new class {
                #[ToArrayOfStrings(trim: true)]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 'world'],
            "hello\nworld",
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 'world'],
            ['hello', 'world'],
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', '42', '1', '2.4'],
            ['hello', 42, true, 2.4],
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 'world'],
            new ArrayObject(['hello', 'world']),
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ["hello\nworld"],
            "hello\nworld",
            new class {
                #[ToArrayOfStrings(splitResolvedValue: false)]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', '', 'world'],
            "hello\n\nworld",
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 2 => 'world'],
            "hello\n\nworld",
            new class {
                #[ToArrayOfStrings(removeEmpty: true)]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', '', ' world', ' good '],
            "hello\n\n world\n good ",
            new class {
                #[ToArrayOfStrings]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 2 => 'world', 3 => 'good'],
            "hello\n\n world\n good ",
            new class {
                #[ToArrayOfStrings(trim: true, removeEmpty: true)]
                public ?array $value = null;
            },
        ];
        yield [
            ['hello', 'world', 'good'],
            'hello,world,good',
            new class {
                #[ToArrayOfStrings(separator: ',')]
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
            'Expected "' . ToArrayOfStrings::class . '", but "' . Counter::class . '" given.',
        );
        $hydrator->hydrate($object);
    }

    public function testMultibyteTrim(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    ToArrayOfStringsResolver::class => new ToArrayOfStringsResolver(multibyte: true),
                ]),
            ),
        );
        $object = new class {
            #[ToArrayOfStrings(trim: true, separator: ',')]
            public ?array $value = null;
        };

        $hydrator->hydrate($object, ['value' => "\u{A0}hello\u{2003},\u{2002}world "]);

        $this->assertSame(['hello', 'world'], $object->value);
    }

    public function testAttributeMultibyteTrim(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[ToArrayOfStrings(trim: true, separator: ',', multibyte: true)]
            public ?array $value = null;
        };

        $hydrator->hydrate($object, ['value' => "\u{A0}hello\u{2003},\u{2002}world "]);

        $this->assertSame(['hello', 'world'], $object->value);
    }

    public function testAttributeMultibyteOverridesResolverDefault(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    ToArrayOfStringsResolver::class => new ToArrayOfStringsResolver(multibyte: true),
                ]),
            ),
        );
        $object = new class {
            #[ToArrayOfStrings(trim: true, separator: ',', multibyte: false)]
            public ?array $value = null;
        };

        $hydrator->hydrate($object, ['value' => "\u{A0}hello\u{2003},\u{2002}world "]);

        $this->assertSame(["\u{A0}hello\u{2003}", "\u{2002}world"], $object->value);
    }

    public function testAttributeEncodingOverridesResolverDefault(): void
    {
        $value = iconv('UTF-8', 'Windows-1251', "  привет  ");
        $expected = iconv('UTF-8', 'Windows-1251', 'привет');

        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    ToArrayOfStringsResolver::class => new ToArrayOfStringsResolver(multibyte: true, encoding: 'UTF-8'),
                ]),
            ),
        );
        $object = new class {
            #[ToArrayOfStrings(trim: true, splitResolvedValue: false, encoding: 'Windows-1251')]
            public ?array $value = null;
        };

        $hydrator->hydrate($object, ['value' => $value]);

        $this->assertSame([$expected], $object->value);
    }
}
