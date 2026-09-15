<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\MultibyteRightTrim;
use Yiisoft\Hydrator\Attribute\Parameter\MultibyteRightTrimResolver;
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

final class MultibyteRightTrimTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield [' test', new MultibyteRightTrim(), ' test '];
        yield ["\u{A0}\u{2002}test", new MultibyteRightTrim(), "\u{A0}\u{2002}test\u{2003} "];
        yield [' test ', new MultibyteRightTrim('t'), ' test '];
        yield ['tes', new MultibyteRightTrim('t'), 'test'];
        yield ["\u{2022}test", new MultibyteRightTrim("\u{2022}"), "\u{2022}test\u{2022}"];
        yield ["\u{430}b", new MultibyteRightTrim("\u{430}\u{44F}"), "\u{430}b\u{44F}"];
        // Unlike `rtrim()`, `mb_rtrim()` does not support the `..` range syntax.
        yield ['m', new MultibyteRightTrim('a..z'), 'm'];
    }

    #[DataProvider('dataBase')]
    public function testBase(string $expected, MultibyteRightTrim $attribute, mixed $value): void
    {
        $resolver = new MultibyteRightTrimResolver();
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
            #[MultibyteRightTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{A0}hello\u{2003}"]);

        $this->assertSame("\u{A0}hello", $object->a);
    }

    public function testNotResolve(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[MultibyteRightTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => new stdClass()]);

        $this->assertNull($object->a);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[MultibyteRightTrim]
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
                    CounterResolver::class => new MultibyteRightTrimResolver(),
                ]),
            ),
        );
        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . MultibyteRightTrim::class . '", but "' . Counter::class . '" given.',
        );
        $hydrator->hydrate($object);
    }

    public function testDefaultCharacters(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    MultibyteRightTrimResolver::class => new MultibyteRightTrimResolver(characters: "\u{2022}"),
                ]),
            ),
        );
        $object = new class {
            #[MultibyteRightTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{2022}test\u{2022}"]);

        $this->assertSame("\u{2022}test", $object->a);
    }

    public function testOverrideDefaultCharacters(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    MultibyteRightTrimResolver::class => new MultibyteRightTrimResolver(characters: '_-'),
                ]),
            ),
        );
        $object = new class {
            #[MultibyteRightTrim(characters: "\u{2022}")]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{2022}test\u{2022}"]);

        $this->assertSame("\u{2022}test", $object->a);
    }
}
