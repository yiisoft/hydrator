<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Attribute\Parameter\ToString;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Hydrator\Tests\Support\StringableObject;
use Yiisoft\Hydrator\Exception\UnexpectedAttributeException;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ToStringTest extends TestCase
{
    public function dataBase(): array
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

    /**
     * @dataProvider dataBase
     */
    public function testBase(string $expected, mixed $value): void
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
