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

final class TrimTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield ['test', new Trim(), ' test '];
        yield [' test ', new Trim('t'), ' test '];
        yield ['es', new Trim('t'), 'test'];
    }

    #[DataProvider('dataBase')]
    public function testBase(string $expected, Trim $attribute, mixed $value): void
    {
        $resolver = new TrimResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
        );

        $result = $resolver->getParameterValue($attribute, $context);

        $this->assertTrue($result->isResolved());
        $this->assertEquals($expected, $result->getValue());
    }

    public function testWithHydrator(): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[Trim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => ' hello ']);

        $this->assertSame('hello', $object->a);
    }

    public function testNotResolve(): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[Trim]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => new stdClass()]);

        $this->assertNull($object->a);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
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
            'Expected "' . Trim::class . '", but "' . Counter::class . '" given.'
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
        $object = new class () {
            #[Trim(characters: '*')]
            public ?string $a = null;
        };

        $hydrator->hydrate($object, ['a' => '*test*']);

        $this->assertSame('test', $object->a);
    }
}
