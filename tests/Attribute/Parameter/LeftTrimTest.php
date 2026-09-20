<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\LeftTrim;
use Yiisoft\Hydrator\Attribute\Parameter\LeftTrimResolver;
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

final class LeftTrimTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield ['test ', new LeftTrim(), ' test '];
        yield [' test ', new LeftTrim('t'), ' test '];
        yield ['est', new LeftTrim('t'), 'test'];
        yield ["test\u{2003} ", new LeftTrim(), " \u{A0}test\u{2003} "];

        yield ["test\u{2003} ", new LeftTrim(multibyte: true), "\u{A0}\u{2002}test\u{2003} "];
        yield [' test ', new LeftTrim('t', multibyte: true), ' test '];
        yield ["b\u{44F}", new LeftTrim("\u{430}\u{44F}", multibyte: true), "\u{430}b\u{44F}"];

        $characters = iconv('UTF-8', 'Windows-1251', 'а');
        $value = iconv('UTF-8', 'Windows-1251', 'атеста');
        $expected = iconv('UTF-8', 'Windows-1251', 'теста');
        yield [$expected, new LeftTrim($characters, multibyte: true, encoding: 'Windows-1251'), $value];
    }

    #[DataProvider('dataBase')]
    public function testBase(string $expected, LeftTrim $attribute, mixed $value): void
    {
        $resolver = new LeftTrimResolver();
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
            #[LeftTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => ' hello ']);

        $this->assertSame('hello ', $object->a);
    }

    public function testNotResolve(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[LeftTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => new stdClass()]);

        $this->assertNull($object->a);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[LeftTrim]
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
                    CounterResolver::class => new LeftTrimResolver(),
                ]),
            ),
        );
        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . LeftTrim::class . '", but "' . Counter::class . '" given.',
        );
        $hydrator->hydrate($object);
    }

    public function testOverrideDefaultCharacters(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    LeftTrimResolver::class => new LeftTrimResolver(characters: '_-'),
                ]),
            ),
        );
        $object = new class {
            #[LeftTrim(characters: '*')]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => '*test*']);

        $this->assertSame('test*', $object->a);
    }

    public function testDefaultMultibyteFromResolver(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    LeftTrimResolver::class => new LeftTrimResolver(multibyte: true),
                ]),
            ),
        );
        $object = new class {
            #[LeftTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{A0}test\u{2003}"]);

        $this->assertSame("test\u{2003}", $object->a);
    }

    public function testOverrideMultibyteFalse(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    LeftTrimResolver::class => new LeftTrimResolver(multibyte: true),
                ]),
            ),
        );
        $object = new class {
            #[LeftTrim(multibyte: false)]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{A0}test\u{2003}"]);

        $this->assertSame("\u{A0}test\u{2003}", $object->a);
    }

    public function testDefaultEncodingFromResolver(): void
    {
        $characters = iconv('UTF-8', 'Windows-1251', 'а');
        $value = iconv('UTF-8', 'Windows-1251', 'атеста');
        $expected = iconv('UTF-8', 'Windows-1251', 'теста');

        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    LeftTrimResolver::class => new LeftTrimResolver(characters: $characters, multibyte: true, encoding: 'Windows-1251'),
                ]),
            ),
        );
        $object = new class {
            #[LeftTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => $value]);

        $this->assertSame($expected, $object->a);
    }

    public function testOverrideEncoding(): void
    {
        $characters = iconv('UTF-8', 'Windows-1251', 'а');
        $value = iconv('UTF-8', 'Windows-1251', 'атеста');
        $expected = iconv('UTF-8', 'Windows-1251', 'теста');

        $resolver = new LeftTrimResolver(multibyte: true, encoding: 'UTF-8');
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );

        $result = $resolver->getParameterValue(new LeftTrim($characters, encoding: 'Windows-1251'), $context);

        $this->assertSame($expected, $result->getValue());
    }

    public static function dataDeprecationNoticeForRangeCharacters(): iterable
    {
        yield 'attribute' => [static fn(): LeftTrim => new LeftTrim('a..z')];
        yield 'resolver' => [static fn(): LeftTrimResolver => new LeftTrimResolver(characters: 'a..z')];
    }

    #[DataProvider('dataDeprecationNoticeForRangeCharacters')]
    public function testDeprecationNoticeForRangeCharacters(Closure $create): void
    {
        $errors = TestHelper::captureErrors($create);

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
            new LeftTrim('a.b');
            new LeftTrim('.');
            new LeftTrim(null);
            new LeftTrimResolver(characters: 'a.b');
            new LeftTrimResolver(characters: '.');
            new LeftTrimResolver(characters: null);
        });

        $this->assertSame([], $errors);
    }

    public function testRangeInNonMultibyteModeRegression(): void
    {
        $resolver = new LeftTrimResolver(multibyte: false);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success('xyztest123'),
            new ArrayData(),
            new Hydrator(),
        );

        TestHelper::captureErrors(static function () use ($resolver, $context, &$value): void {
            $value = $resolver->getParameterValue(new LeftTrim('a..z'), $context)->getValue();
        });

        $this->assertSame('123', $value);
    }

    #[TestWith(["\u{430}..\u{44F}", "\u{436}Hello\u{44E}", "Hello\u{44E}", null])]
    #[TestWith(["\u{4E00}..\u{5341}", "\u{4E00}test\u{5341}", "test\u{5341}", null])]
    #[TestWith(['..z', '.zXz.', 'Xz.', "Invalid '..'-range, no character to the left of '..'"])]
    #[TestWith(['a..', 'aXa..', 'Xa..', "Invalid '..'-range, no character to the right of '..'"])]
    #[TestWith(['c..a', 'caXcac', 'Xcac', "Invalid '..'-range, '..'-range needs to be incrementing"])]
    #[TestWith(['a..b..c', 'abXcba..b..c', 'Xcba..b..c', "Invalid '..'-range"])]
    public function testRangeInMultibyteMode(
        string $characters,
        string $value,
        string $expectedResult,
        ?string $expectedWarning,
    ): void {
        $resolver = new LeftTrimResolver(multibyte: true);
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );

        $errors = TestHelper::captureErrors(
            static function () use ($resolver, $context, $characters, &$result): void {
                $result = $resolver->getParameterValue(new LeftTrim($characters), $context)->getValue();
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
