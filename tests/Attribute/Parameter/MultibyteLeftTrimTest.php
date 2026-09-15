<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\MultibyteLeftTrim;
use Yiisoft\Hydrator\Attribute\Parameter\MultibyteLeftTrimResolver;
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

final class MultibyteLeftTrimTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield ['test ', new MultibyteLeftTrim(), ' test '];
        yield ["test\u{2003} ", new MultibyteLeftTrim(), "\u{A0}\u{2002}test\u{2003} "];
        yield [' test ', new MultibyteLeftTrim('t'), ' test '];
        yield ['est', new MultibyteLeftTrim('t'), 'test'];
        yield ["test\u{2022}", new MultibyteLeftTrim("\u{2022}"), "\u{2022}test\u{2022}"];
        yield ["b\u{44F}", new MultibyteLeftTrim("\u{430}\u{44F}"), "\u{430}b\u{44F}"];
        // Unlike `ltrim()`, `mb_ltrim()` does not support the `..` range syntax.
        yield ['m', new MultibyteLeftTrim('a..z'), 'm'];
    }

    #[DataProvider('dataBase')]
    public function testBase(string $expected, MultibyteLeftTrim $attribute, mixed $value): void
    {
        $resolver = new MultibyteLeftTrimResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );

        $result = $resolver->getParameterValue($attribute, $context);

        $this->assertTrue($result->isResolved());
        $this->assertSame($expected, $result->getValue());
    }

    public function testWithHydrator(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[MultibyteLeftTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{A0}hello\u{2003}"]);

        $this->assertSame("hello\u{2003}", $object->a);
    }

    public function testNotResolve(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[MultibyteLeftTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => new stdClass()]);

        $this->assertNull($object->a);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[MultibyteLeftTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['b' => "\u{A0}test\u{2003}"]);

        $this->assertNull($object->a);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    CounterResolver::class => new MultibyteLeftTrimResolver(),
                ]),
            ),
        );
        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . MultibyteLeftTrim::class . '", but "' . Counter::class . '" given.',
        );
        $hydrator->hydrate($object);
    }

    public function testDefaultCharacters(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    MultibyteLeftTrimResolver::class => new MultibyteLeftTrimResolver(characters: "\u{2022}"),
                ]),
            ),
        );
        $object = new class {
            #[MultibyteLeftTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{2022}test\u{2022}"]);

        $this->assertSame("test\u{2022}", $object->a);
    }

    public function testOverrideDefaultCharacters(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    MultibyteLeftTrimResolver::class => new MultibyteLeftTrimResolver(characters: '_-'),
                ]),
            ),
        );
        $object = new class {
            #[MultibyteLeftTrim(characters: "\u{2022}")]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{2022}test\u{2022}"]);

        $this->assertSame("test\u{2022}", $object->a);
    }
}
