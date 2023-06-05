<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Container;

use PHPUnit\Framework\TestCase;
use Throwable;
use Yiisoft\Hydrator\Attribute\Parameter\Di;
use Yiisoft\Hydrator\Container\AttributeResolverNotFoundException;
use Yiisoft\Hydrator\Container\EmptyAttributeResolverContainer;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Classes\EngineInterface;

final class EmptyAttributeResolverContainerTest extends TestCase
{
    public function testNotFound(): void
    {
        $hydrator = new Hydrator();

        $object = new class () {
            #[Di]
            private EngineInterface $service;
        };

        $exception = null;
        try {
            $hydrator->hydrate($object);
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(AttributeResolverNotFoundException::class, $exception);
        $this->assertSame(
            'Attribute resolver "Yiisoft\Hydrator\Attribute\Parameter\DiResolver" not found.',
            $exception->getMessage()
        );
        $this->assertSame(
            'Attribute resolver not found.',
            $exception->getName()
        );
        $this->assertMatchesRegularExpression(
            '~You use attribute with separate resolver "Yiisoft\\\Hydrator\\\Attribute\\\Parameter\\\DiResolver", but~',
            $exception->getSolution()
        );
    }

    public function testHas(): void
    {
        $container = new EmptyAttributeResolverContainer();

        $this->assertFalse($container->has('anything'));
    }
}
