<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\MultibyteTrim;
use Yiisoft\Hydrator\Attribute\Parameter\MultibyteTrimResolver;
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

final class MultibyteTrimTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield ['test', new MultibyteTrim(), ' test '];
        yield ['test', new MultibyteTrim(), "\u{A0}\u{2002}test\u{2003} "];
        yield [' test ', new MultibyteTrim('t'), ' test '];
        yield ['es', new MultibyteTrim('t'), 'test'];
        yield ['test', new MultibyteTrim("\u{2022}"), "\u{2022}test\u{2022}"];
        yield ['b', new MultibyteTrim("\u{430}\u{44F}"), "\u{430}b\u{44F}"];
        // Unlike `trim()`, `mb_trim()` does not support the `..` range syntax.
        yield ['m', new MultibyteTrim('a..z'), 'm'];
    }

    #[DataProvider('dataBase')]
    public function testBase(string $expected, MultibyteTrim $attribute, mixed $value): void
    {
        $resolver = new MultibyteTrimResolver();
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
            #[MultibyteTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{A0}hello\u{2003}"]);

        $this->assertSame('hello', $object->a);
    }

    public function testNotResolve(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[MultibyteTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => new stdClass()]);

        $this->assertNull($object->a);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class {
            #[MultibyteTrim]
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
                    CounterResolver::class => new MultibyteTrimResolver(),
                ]),
            ),
        );
        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . MultibyteTrim::class . '", but "' . Counter::class . '" given.',
        );
        $hydrator->hydrate($object);
    }

    public function testDefaultCharacters(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    MultibyteTrimResolver::class => new MultibyteTrimResolver(characters: "\u{2022}"),
                ]),
            ),
        );
        $object = new class {
            #[MultibyteTrim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{2022}test\u{2022}"]);

        $this->assertSame('test', $object->a);
    }

    public function testOverrideDefaultCharacters(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    MultibyteTrimResolver::class => new MultibyteTrimResolver(characters: '_-'),
                ]),
            ),
        );
        $object = new class {
            #[MultibyteTrim(characters: "\u{2022}")]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => "\u{2022}test\u{2022}"]);

        $this->assertSame('test', $object->a);
    }
}
