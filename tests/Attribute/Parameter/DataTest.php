<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Attribute\Parameter\Data;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class DataTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = new class () {
            #[Data('a')]
            public ?int $x = null;

            #[Data('b')]
            public ?int $y = null;
        };

        $hydrator->hydrate(
            $object,
            data: [
                'a' => 99,
                'b' => 88,
                'x' => 77,
                'y' => 42,
            ],
        );

        $this->assertSame(99, $object->x);
        $this->assertSame(88, $object->y);
    }

    public function testWholeData(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = new class () {
            #[Data]
            public array $data = [];
        };

        $hydrator->hydrate(
            $object,
            data: ['a' => 1, 'b' => 2],
        );

        $this->assertSame(['a' => 1, 'b' => 2], $object->data);
    }

    public function testPath(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = new class () {
            #[Data('nested.n')]
            public ?int $y = null;

            #[Data(['nested', 'nested2', 'n'])]
            public ?int $z = null;
        };

        $hydrator->hydrate(
            $object,
            data: [
                'nested' => [
                    'n' => 2,
                    'nested2' => [
                        'n' => 3,
                    ],
                ],
            ],
        );

        $this->assertSame(2, $object->y);
        $this->assertSame(3, $object->z);
    }

    public function testMapping(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = new class () {
            #[Data('a')]
            public ?int $x = null;

            #[Data('b')]
            public ?int $y = null;

            #[Data('c')]
            public ?int $z = null;
        };

        $hydrator->hydrate(
            $object,
            data: [
                'value' => 1,
                'nested' => [
                    'n' => 2,
                    'nested2' => [
                        'n' => 3,
                    ],
                ],
            ],
            map: [
                'a' => 'value',
                'b' => ['nested', 'n'],
                'c' => 'nested.nested2.n',
            ]
        );

        $this->assertSame(1, $object->x);
        $this->assertSame(2, $object->y);
        $this->assertSame(3, $object->z);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            new SimpleContainer([CounterResolver::class => new Data()])
        );

        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . Data::class . '", but "' . Counter::class . '" given.');
        $hydrator->hydrate($object);
    }
}
