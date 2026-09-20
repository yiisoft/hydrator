<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
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

    public function testDeprecationNoticeFromAttributeForRangeCharacters(): void
    {
        $errors = TestHelper::captureErrors(static function (): void {
            new Trim('a..z');
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_DEPRECATED, $errors[0][0]);
        $this->assertSame(
            'The ".." range syntax of the "characters" parameter is deprecated and will be removed in the next major version.',
            $errors[0][1],
        );
    }

    public function testDeprecationNoticeFromResolverForRangeCharacters(): void
    {
        $errors = TestHelper::captureErrors(static function (): void {
            new TrimResolver(characters: 'a..z');
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_DEPRECATED, $errors[0][0]);
        $this->assertSame(
            'The ".." range syntax of the "characters" parameter is deprecated and will be removed in the next major version.',
            $errors[0][1],
        );
    }

    public function testNoDeprecationNoticeForSingleDotOrNullCharacters(): void
    {
        $errors = TestHelper::captureErrors(static function (): void {
            new Trim('a.b');
            new Trim('.');
            new Trim(null);
            new TrimResolver(characters: 'a.b');
            new TrimResolver(characters: '.');
            new TrimResolver(characters: null);
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

    public function testRangeInMultibyteMode(): void
    {
        $resolver = new TrimResolver(multibyte: true);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success("\u{436}Hello\u{44E}"),
            new ArrayData(),
            new Hydrator(),
        );

        $result = TestHelper::captureErrors(static function () use ($resolver, $context, &$value): void {
            $value = $resolver->getParameterValue(new Trim("\u{430}..\u{44F}"), $context)->getValue();
        });

        $this->assertSame('Hello', $value);
    }

    public function testCjkRangeInMultibyteMode(): void
    {
        $resolver = new TrimResolver(multibyte: true);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success("\u{4E00}test\u{5341}"),
            new ArrayData(),
            new Hydrator(),
        );

        TestHelper::captureErrors(static function () use ($resolver, $context, &$value): void {
            $value = $resolver->getParameterValue(new Trim("\u{4E00}..\u{5341}"), $context)->getValue();
        });

        $this->assertSame('test', $value);
    }

    public function testInvalidRangeNoLeftCharacterInMultibyteMode(): void
    {
        $resolver = new TrimResolver(multibyte: true);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success('.zXz.'),
            new ArrayData(),
            new Hydrator(),
        );

        $errors = TestHelper::captureErrors(static function () use ($resolver, $context, &$value): void {
            $value = $resolver->getParameterValue(new Trim('..z'), $context)->getValue();
        });

        $warnings = array_filter($errors, static fn(array $error): bool => $error[0] === E_USER_WARNING);
        $this->assertCount(1, $warnings);
        $this->assertSame("Invalid '..'-range, no character to the left of '..'", reset($warnings)[1]);
        $this->assertSame('X', $value);
    }

    public function testInvalidRangeNoRightCharacterInMultibyteMode(): void
    {
        $resolver = new TrimResolver(multibyte: true);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success('aXa..'),
            new ArrayData(),
            new Hydrator(),
        );

        $errors = TestHelper::captureErrors(static function () use ($resolver, $context, &$value): void {
            $value = $resolver->getParameterValue(new Trim('a..'), $context)->getValue();
        });

        $warnings = array_filter($errors, static fn(array $error): bool => $error[0] === E_USER_WARNING);
        $this->assertCount(1, $warnings);
        $this->assertSame("Invalid '..'-range, no character to the right of '..'", reset($warnings)[1]);
        $this->assertSame('X', $value);
    }

    public function testInvalidRangeNotIncrementingInMultibyteMode(): void
    {
        $resolver = new TrimResolver(multibyte: true);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success('caXcac'),
            new ArrayData(),
            new Hydrator(),
        );

        $errors = TestHelper::captureErrors(static function () use ($resolver, $context, &$value): void {
            $value = $resolver->getParameterValue(new Trim('c..a'), $context)->getValue();
        });

        $warnings = array_filter($errors, static fn(array $error): bool => $error[0] === E_USER_WARNING);
        $this->assertCount(1, $warnings);
        $this->assertSame("Invalid '..'-range, '..'-range needs to be incrementing", reset($warnings)[1]);
        $this->assertSame('X', $value);
    }

    public function testInvalidRangeAmbiguousInMultibyteMode(): void
    {
        $resolver = new TrimResolver(multibyte: true);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success('abXcba..b..c'),
            new ArrayData(),
            new Hydrator(),
        );

        $errors = TestHelper::captureErrors(static function () use ($resolver, $context, &$value): void {
            $value = $resolver->getParameterValue(new Trim('a..b..c'), $context)->getValue();
        });

        $warnings = array_filter($errors, static fn(array $error): bool => $error[0] === E_USER_WARNING);
        $this->assertCount(1, $warnings);
        $this->assertSame("Invalid '..'-range", reset($warnings)[1]);
        $this->assertSame('X', $value);
    }
}
