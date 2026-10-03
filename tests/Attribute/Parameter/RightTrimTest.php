<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\RightTrim;
use Yiisoft\Hydrator\Attribute\Parameter\RightTrimResolver;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;

use const E_USER_DEPRECATED;
use const E_USER_WARNING;

final class RightTrimTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield [' test', new RightTrim(), ' test '];
        yield [' test ', new RightTrim('t'), ' test '];
        yield ['tes', new RightTrim('t'), 'test'];
        yield [" \u{A0}test\u{2003}", new RightTrim(), " \u{A0}test\u{2003} "];

        yield ["\u{A0}\u{2002}test", new RightTrim(multibyte: true), "\u{A0}\u{2002}test\u{2003} "];
        yield [' test ', new RightTrim('t', multibyte: true), ' test '];
        yield ["\u{430}b", new RightTrim("\u{430}\u{44F}", multibyte: true), "\u{430}b\u{44F}"];

        $characters = mb_convert_encoding('а', 'Windows-1251', 'UTF-8');
        $value = mb_convert_encoding('атеста', 'Windows-1251', 'UTF-8');
        $expected = mb_convert_encoding('атест', 'Windows-1251', 'UTF-8');
        yield [$expected, new RightTrim($characters, multibyte: true, encoding: 'Windows-1251'), $value];
    }

    #[DataProvider('dataBase')]
    public function testBase(string $expected, RightTrim $attribute, mixed $value): void
    {
        $resolver = new RightTrimResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );

        $result = $resolver->getParameterValue($attribute, $context);

        $this->assertTrue($result->isResolved());
        $this->assertEquals($expected, $result->getValue());
    }

    public function testWithHydrator(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[RightTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => ' hello ']);

        $this->assertSame(' hello', $object->a);
    }

    public function testNotResolve(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[RightTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => new stdClass()]);

        $this->assertNull($object->a);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[RightTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['b' => ' test ']);

        $this->assertNull($object->a);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    CounterResolver::class => new RightTrimResolver(),
                ]),
            ),
        );
        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . RightTrim::class . '", but "' . Counter::class . '" given.',
        );
        $hydrator->hydrate($object);
    }

    public function testOverrideDefaultCharacters(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    RightTrimResolver::class => new RightTrimResolver(characters: '_-'),
                ]),
            ),
        );
        $object = new class {
            #[RightTrim(characters: '*')]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => '*test*']);

        $this->assertSame('*test', $object->a);
    }

    public function testDefaultMultibyteFromResolver(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    RightTrimResolver::class => new RightTrimResolver(multibyte: true),
                ]),
            ),
        );
        $object = new class {
            #[RightTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{A0}test\u{2003}"]);

        $this->assertSame("\u{A0}test", $object->a);
    }

    public function testOverrideMultibyteFalse(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    RightTrimResolver::class => new RightTrimResolver(multibyte: true),
                ]),
            ),
        );
        $object = new class {
            #[RightTrim(multibyte: false)]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{A0}test\u{2003}"]);

        $this->assertSame("\u{A0}test\u{2003}", $object->a);
    }

    public function testDefaultEncodingFromResolver(): void
    {
        $characters = mb_convert_encoding('а', 'Windows-1251', 'UTF-8');
        $value = mb_convert_encoding('атеста', 'Windows-1251', 'UTF-8');
        $expected = mb_convert_encoding('атест', 'Windows-1251', 'UTF-8');

        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    RightTrimResolver::class => new RightTrimResolver(characters: $characters, multibyte: true, encoding: 'Windows-1251'),
                ]),
            ),
        );
        $object = new class {
            #[RightTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => $value]);

        $this->assertSame($expected, $object->a);
    }

    public function testOverrideEncoding(): void
    {
        $characters = mb_convert_encoding('а', 'Windows-1251', 'UTF-8');
        $value = mb_convert_encoding('атеста', 'Windows-1251', 'UTF-8');
        $expected = mb_convert_encoding('атест', 'Windows-1251', 'UTF-8');

        $resolver = new RightTrimResolver(multibyte: true, encoding: 'UTF-8');
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );

        $result = $resolver->getParameterValue(new RightTrim($characters, encoding: 'Windows-1251'), $context);

        $this->assertSame($expected, $result->getValue());
    }

    public static function dataDeprecationNoticeForRangeCharacters(): iterable
    {
        yield 'attribute' => [new RightTrim('a..z'), new RightTrimResolver()];
        yield 'resolver' => [new RightTrim(), new RightTrimResolver(characters: 'a..z')];
        yield 'multibyte' => [new RightTrim('a..z', multibyte: true), new RightTrimResolver()];
        yield 'multibyte-utf-16' => [
            new RightTrim(mb_convert_encoding('a..z', 'UTF-16LE', 'UTF-8'), multibyte: true, encoding: 'UTF-16LE'),
            new RightTrimResolver(),
        ];
        yield 'multibyte-utf-16-from-resolver' => [
            new RightTrim(mb_convert_encoding('a..z', 'UTF-16BE', 'UTF-8')),
            new RightTrimResolver(multibyte: true, encoding: 'UTF-16BE'),
        ];
    }

    #[DataProvider('dataDeprecationNoticeForRangeCharacters')]
    public function testDeprecationNoticeForRangeCharacters(RightTrim $attribute, RightTrimResolver $resolver): void
    {
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success(''),
            new ArrayData(),
            new Hydrator(),
        );

        $errors = TestHelper::captureErrors(static function () use ($resolver, $attribute, $context): void {
            $resolver->getParameterValue($attribute, $context);
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_DEPRECATED, $errors[0][0]);
        $this->assertSame(
            'The ".." range syntax of the "characters" parameter is deprecated and will be removed in the next major version.',
            $errors[0][1],
        );
    }

    public static function dataNoDeprecationNotice(): iterable
    {
        yield 'single-dot' => [new RightTrim('a.b')];
        yield 'dot' => [new RightTrim('.')];
        yield 'null' => [new RightTrim(null)];
        yield 'multibyte-single-dot' => [new RightTrim('a.b', multibyte: true)];
        yield 'multibyte-utf-16-dot-bytes' => [
            new RightTrim(
                mb_convert_encoding("\u{2E41}\u{412E}", 'UTF-16LE', 'UTF-8'),
                multibyte: true,
                encoding: 'UTF-16LE',
            ),
        ];
    }

    #[DataProvider('dataNoDeprecationNotice')]
    public function testNoDeprecationNotice(RightTrim $attribute): void
    {
        $resolver = new RightTrimResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success('test'),
            new ArrayData(),
            new Hydrator(),
        );

        $errors = TestHelper::captureErrors(static function () use ($resolver, $attribute, $context): void {
            $resolver->getParameterValue($attribute, $context);
        });

        $this->assertSame([], $errors);
    }

    public function testNoDeprecationNoticeOnCreate(): void
    {
        $errors = TestHelper::captureErrors(static function (): void {
            new RightTrim('a..z');
            new RightTrimResolver(characters: 'a..z');
        });

        $this->assertSame([], $errors);
    }

    public function testRangeInNonMultibyteModeRegression(): void
    {
        $resolver = new RightTrimResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success('xyztest123'),
            new ArrayData(),
            new Hydrator(),
        );

        TestHelper::captureErrors(static function () use ($resolver, $context, &$value): void {
            $value = $resolver->getParameterValue(new RightTrim('a..z'), $context)->getValue();
        });

        $this->assertSame('xyztest123', $value);
    }

    #[TestWith(["\u{430}..\u{44F}", "\u{436}Hello\u{44E}", "\u{436}Hello", null])]
    #[TestWith(["\u{4E00}..\u{5341}", "\u{4E00}test\u{5341}", "\u{4E00}test", null])]
    #[TestWith(['..z', '.zXz.', '.zX', "Invalid '..'-range, no character to the left of '..'"])]
    #[TestWith(['a..', 'aXa..', 'aX', "Invalid '..'-range, no character to the right of '..'"])]
    #[TestWith(['c..a', 'caXcac', 'caX', "Invalid '..'-range, '..'-range needs to be incrementing"])]
    #[TestWith(['a..b..c', 'abXcba..b..c', 'abX', "Invalid '..'-range"])]
    public function testRangeInMultibyteMode(
        string $characters,
        string $value,
        string $expectedResult,
        ?string $expectedWarning,
    ): void {
        $resolver = new RightTrimResolver(multibyte: true);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );

        $errors = TestHelper::captureErrors(
            static function () use ($resolver, $context, $characters, &$result): void {
                $result = $resolver->getParameterValue(new RightTrim($characters), $context)->getValue();
            },
        );

        if ($expectedWarning !== null) {
            $warnings = array_filter($errors, static fn(array $error): bool => $error[0] === E_USER_WARNING);
            $this->assertCount(1, $warnings);
            $this->assertSame($expectedWarning, reset($warnings)[1]);
        }
        $this->assertSame($expectedResult, $result);
    }
}
