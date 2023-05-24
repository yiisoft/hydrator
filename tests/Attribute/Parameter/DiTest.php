<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;
use Yiisoft\Hydrator\Attribute\Parameter\Di;
use Yiisoft\Hydrator\Attribute\Parameter\DiNotFoundException;
use Yiisoft\Hydrator\Attribute\Parameter\DiResolver;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Hydrator\Tests\Support\Classes\DiSingle;
use Yiisoft\Hydrator\Tests\Support\Classes\DiSingleConstructor;
use Yiisoft\Hydrator\Tests\Support\Classes\DiSingleNulledWithDefault;
use Yiisoft\Hydrator\Tests\Support\Classes\DiSingleNulledWithDefaultConstructor;
use Yiisoft\Hydrator\Tests\Support\Classes\DiSingleWithoutType;
use Yiisoft\Hydrator\Tests\Support\Classes\DiSingleWithoutTypeConstructor;
use Yiisoft\Hydrator\Tests\Support\Classes\DiUnion;
use Yiisoft\Hydrator\Tests\Support\Classes\DiUnionWithDefault;
use Yiisoft\Hydrator\Tests\Support\Classes\DiUnionWithDefaultConstructor;
use Yiisoft\Hydrator\Tests\Support\Classes\Engine1;
use Yiisoft\Hydrator\Tests\Support\Classes\EngineInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class DiTest extends TestCase
{
    public function testSingle(): void
    {
        $engine = new Engine1();
        $hydrator = $this->createHydrator([EngineInterface::class => $engine]);

        $object = $hydrator->create(DiSingle::class);

        $this->assertSame($engine, $object->engine);
    }

    public function testSingleWithoutTypeNotResolved(): void
    {
        $hydrator = $this->createHydrator();

        $object = $hydrator->create(DiSingleWithoutType::class);

        $this->assertNull($object->engine);
    }

    public function testSingleWithoutTypeConstructorNotResolved(): void
    {
        $hydrator = $this->createHydrator();

        $this->expectException(DiNotFoundException::class);
        $this->expectExceptionMessage(
            'Constructor parameter "engine" of class "'
            . DiSingleWithoutTypeConstructor::class
            . '" without type not resolved.'
        );
        $hydrator->create(DiSingleWithoutTypeConstructor::class);
    }

    public function testSingleNotResolved(): void
    {
        $hydrator = $this->createHydrator();

        $exception = null;
        try {
            $hydrator->create(DiSingle::class);
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(DiNotFoundException::class, $exception);
        $this->assertSame(
            'Class property "'
            . DiSingle::class
            . '::$engine" with type "'
            . EngineInterface::class
            . '" not resolved.',
            $exception->getMessage()
        );
        $this->assertInstanceOf(NotFoundExceptionInterface::class, $exception->getPrevious());
    }

    public function testSingleConstructor(): void
    {
        $engine = new Engine1();
        $hydrator = $this->createHydrator([EngineInterface::class => $engine]);

        $object = $hydrator->create(DiSingleConstructor::class);

        $this->assertSame($engine, $object->engine);
    }

    public function testSingleConstructorNotResolved(): void
    {
        $hydrator = $this->createHydrator();


        $this->expectException(DiNotFoundException::class);
        $this->expectExceptionMessage(
            'Constructor parameter "engine" of class "'
            . DiSingleConstructor::class
            . '" with type "'
            . EngineInterface::class
            . '" not resolved.'
        );
        $hydrator->create(DiSingleConstructor::class);
    }

    public function testSingleNulledWithDefault(): void
    {
        $engine = new Engine1();
        $hydrator = $this->createHydrator([EngineInterface::class => $engine]);

        $object = $hydrator->create(DiSingleNulledWithDefault::class);

        $this->assertSame($engine, $object->engine);
    }

    public function testSingleNulledWithDefaultConstructor(): void
    {
        $engine = new Engine1();
        $hydrator = $this->createHydrator([EngineInterface::class => $engine]);

        $object = $hydrator->create(DiSingleNulledWithDefaultConstructor::class);

        $this->assertSame($engine, $object->engine);
    }

    public function testSingleNulledWithDefaultNotResolved(): void
    {
        $hydrator = $this->createHydrator();

        $object = $hydrator->create(DiSingleNulledWithDefault::class);

        $this->assertNull($object->engine);
    }

    public function testSingleNulledWithDefaultConstructorNotResolved(): void
    {
        $hydrator = $this->createHydrator();

        $object = $hydrator->create(DiSingleNulledWithDefaultConstructor::class);

        $this->assertNull($object->engine);
    }

    public function testUnion(): void
    {
        $engine = new Engine1();
        $hydrator = $this->createHydrator([EngineInterface::class => $engine]);

        $object = $hydrator->create(DiUnion::class);

        $this->assertSame($engine, $object->engine1);
        $this->assertSame($engine, $object->engine2);
    }

    public function testUnionNotResolved(): void
    {
        $hydrator = $this->createHydrator();

        $this->expectException(DiNotFoundException::class);
        $this->expectExceptionMessage(
            'Class property "' . DiUnion::class . '::$engine1" with type "' .
            EngineInterface::class . '|string" not resolved.'
        );
        $hydrator->create(DiUnion::class);
    }

    public function testUnionWithDefault(): void
    {
        $engine = new Engine1();
        $hydrator = $this->createHydrator([EngineInterface::class => $engine]);

        $object = $hydrator->create(DiUnionWithDefault::class);

        $this->assertSame($engine, $object->engine1);
        $this->assertSame($engine, $object->engine2);
    }

    public function testUnionWithDefaultNotResolved(): void
    {
        $hydrator = $this->createHydrator();

        $object = $hydrator->create(DiUnionWithDefault::class);

        $this->assertSame('', $object->engine1);
        $this->assertSame('', $object->engine2);
    }

    public function testUnionWithDefaultConstructor(): void
    {
        $engine = new Engine1();
        $hydrator = $this->createHydrator([EngineInterface::class => $engine]);

        $object = $hydrator->create(DiUnionWithDefaultConstructor::class);

        $this->assertSame($engine, $object->engine1);
        $this->assertSame($engine, $object->engine2);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            new SimpleContainer([CounterResolver::class => new DiResolver(new SimpleContainer())])
        );

        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . Di::class . '", but "' . Counter::class . '" given.');
        $hydrator->hydrate($object);
    }

    private function createHydrator(array $definitions = []): Hydrator
    {
        return new Hydrator(
            new SimpleContainer([
                DiResolver::class => new DiResolver(
                    new SimpleContainer($definitions)
                ),
            ]),
        );
    }
}
