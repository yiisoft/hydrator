<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\ToString;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Hydrator\Tests\Support\StringableObject;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ToStringTest extends TestCase
{
    public static function dataBase(): array
    {
        return [
            ['99', 99],
            ['1', true],
            ['1.1', 1.1],
            ['red', 'red'],
            ['', null],
            ['test', new StringableObject('test')],
            ['', tmpfile()],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(mixed $expected, mixed $value): void
    {
        $attribute = new ToString();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );

        $result = $attribute->getParameterValue($attribute, $context);

        $this->assertTrue($result->isResolved());
        $this->assertSame($expected, $result->getValue());
    }

    #[DataProvider('dataBase')]
    public function testBaseWithHydrator(string $expected, mixed $value): void
    {
        $hydrator = new Hydrator();

        $object = new class () {
            #[ToString]
            public string $a = '...';
        };

        $hydrator->hydrate($object, ['a' => $value]);

        $this->assertSame($expected, $object->a);
    }

    public function testNotResolved(): void
    {
        $hydrator = new Hydrator();

        $object = new class () {
            #[ToString]
            public string $a = '...';
        };

        $hydrator->hydrate($object);

        $this->assertSame('...', $object->a);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    CounterResolver::class => new ToString(),
                ]),
            ),
        );

        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . ToString::class . '", but "' . Counter::class . '" given.');
        $hydrator->hydrate($object);
    }
}
