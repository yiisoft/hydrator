<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Hydrator\Attribute\Parameter\TrimResolver;
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

final class TrimTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield ['test', new Trim(), ' test '];
        yield [' test ', new Trim('t'), ' test '];
        yield ['es', new Trim('t'), 'test'];
        yield ["\u{A0}test\u{2003}", new Trim(), " \u{A0}test\u{2003} "];

        yield ['test', new Trim(multibyte: true), "\u{A0}\u{2002}test\u{2003} "];
        yield [' test ', new Trim('t', multibyte: true), ' test '];
        yield ['b', new Trim("\u{430}\u{44F}", multibyte: true), "\u{430}b\u{44F}"];

        $characters = iconv('UTF-8', 'Windows-1251', 'а');
        $value = iconv('UTF-8', 'Windows-1251', 'атеста');
        $expected = iconv('UTF-8', 'Windows-1251', 'тест');
        yield [$expected, new Trim($characters, multibyte: true, encoding: 'Windows-1251'), $value];
    }

    #[DataProvider('dataBase')]
    public function testBase(string $expected, Trim $attribute, mixed $value): void
    {
        $resolver = new TrimResolver();
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
            #[Trim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => ' hello ']);

        $this->assertSame('hello', $object->a);
    }

    public function testNotResolve(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[Trim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => new stdClass()]);

        $this->assertNull($object->a);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[Trim]
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
                    CounterResolver::class => new TrimResolver(),
                ]),
            ),
        );
        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . Trim::class . '", but "' . Counter::class . '" given.',
        );
        $hydrator->hydrate($object);
    }

    public function testOverrideDefaultCharacters(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    TrimResolver::class => new TrimResolver(characters: '_-'),
                ]),
            ),
        );
        $object = new class {
            #[Trim(characters: '*')]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => '*test*']);

        $this->assertSame('test', $object->a);
    }

    public function testDefaultMultibyteFromResolver(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    TrimResolver::class => new TrimResolver(multibyte: true),
                ]),
            ),
        );
        $object = new class {
            #[Trim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{A0}test\u{2003}"]);

        $this->assertSame('test', $object->a);
    }

    public function testOverrideMultibyteFalse(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    TrimResolver::class => new TrimResolver(multibyte: true),
                ]),
            ),
        );
        $object = new class {
            #[Trim(multibyte: false)]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{A0}test\u{2003}"]);

        $this->assertSame("\u{A0}test\u{2003}", $object->a);
    }

    public function testDefaultEncodingFromResolver(): void
    {
        $characters = iconv('UTF-8', 'Windows-1251', 'а');
        $value = iconv('UTF-8', 'Windows-1251', 'атеста');
        $expected = iconv('UTF-8', 'Windows-1251', 'тест');

        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    TrimResolver::class => new TrimResolver(characters: $characters, multibyte: true, encoding: 'Windows-1251'),
                ]),
            ),
        );
        $object = new class {
            #[Trim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => $value]);

        $this->assertSame($expected, $object->a);
    }

    public function testOverrideEncoding(): void
    {
        $characters = iconv('UTF-8', 'Windows-1251', 'а');
        $value = iconv('UTF-8', 'Windows-1251', 'атеста');
        $expected = iconv('UTF-8', 'Windows-1251', 'тест');

        $resolver = new TrimResolver(multibyte: true, encoding: 'UTF-8');
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );

        $result = $resolver->getParameterValue(new Trim($characters, encoding: 'Windows-1251'), $context);

        $this->assertSame($expected, $result->getValue());
    }

    public static function dataDeprecationNoticeForRangeCharacters(): iterable
    {
        yield 'attribute' => [new Trim('a..z'), new TrimResolver()];
        yield 'resolver' => [new Trim(), new TrimResolver(characters: 'a..z')];
        yield 'multibyte' => [new Trim('a..z', multibyte: true), new TrimResolver()];
        yield 'multibyte-utf-16' => [
            new Trim(mb_convert_encoding('a..z', 'UTF-16LE', 'UTF-8'), multibyte: true, encoding: 'UTF-16LE'),
            new TrimResolver(),
        ];
        yield 'multibyte-utf-16-from-resolver' => [
            new Trim(mb_convert_encoding('a..z', 'UTF-16BE', 'UTF-8')),
            new TrimResolver(multibyte: true, encoding: 'UTF-16BE'),
        ];
    }

    #[DataProvider('dataDeprecationNoticeForRangeCharacters')]
    public function testDeprecationNoticeForRangeCharacters(Trim $attribute, TrimResolver $resolver): void
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
        yield 'single-dot' => [new Trim('a.b')];
        yield 'dot' => [new Trim('.')];
        yield 'null' => [new Trim(null)];
        yield 'multibyte-single-dot' => [new Trim('a.b', multibyte: true)];
        yield 'multibyte-utf-16-dot-bytes' => [
            new Trim(
                mb_convert_encoding("\u{2E41}\u{412E}", 'UTF-16LE', 'UTF-8'),
                multibyte: true,
                encoding: 'UTF-16LE',
            ),
        ];
    }

    #[DataProvider('dataNoDeprecationNotice')]
    public function testNoDeprecationNotice(Trim $attribute): void
    {
        $resolver = new TrimResolver();
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
            new Trim('a..z');
            new TrimResolver(characters: 'a..z');
        });

        $this->assertSame([], $errors);
    }

    public function testRangeInNonMultibyteModeRegression(): void
    {
        $resolver = new TrimResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success('xyztest123'),
            new ArrayData(),
            new Hydrator(),
        );

        TestHelper::captureErrors(static function () use ($resolver, $context, &$value): void {
            $value = $resolver->getParameterValue(new Trim('a..z'), $context)->getValue();
        });

        $this->assertSame('123', $value);
    }

    #[TestWith(["\u{430}..\u{44F}", "\u{436}Hello\u{44E}", 'Hello', null])]
    #[TestWith(["\u{4E00}..\u{5341}", "\u{4E00}test\u{5341}", 'test', null])]
    #[TestWith(['..z', '.zXz.', 'X', "Invalid '..'-range, no character to the left of '..'"])]
    #[TestWith(['a..', 'aXa..', 'X', "Invalid '..'-range, no character to the right of '..'"])]
    #[TestWith(['c..a', 'caXcac', 'X', "Invalid '..'-range, '..'-range needs to be incrementing"])]
    #[TestWith(['a..b..c', 'abXcba..b..c', 'X', "Invalid '..'-range"])]
    public function testRangeInMultibyteMode(
        string $characters,
        string $value,
        string $expectedResult,
        ?string $expectedWarning,
    ): void {
        $resolver = new TrimResolver(multibyte: true);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );

        $errors = TestHelper::captureErrors(
            static function () use ($resolver, $context, $characters, &$result): void {
                $result = $resolver->getParameterValue(new Trim($characters), $context)->getValue();
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
