<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Container;

use PHPUnit\Framework\TestCase;
use Throwable;
use Yiisoft\Hydrator\Container\DependencyNotFoundException;
use Yiisoft\Hydrator\Container\EmptyDependencyContainer;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Classes\Car;

final class EmptyDependencyContainerTest extends TestCase
{
    public function testNotFound(): void
    {
        $hydrator = new Hydrator();

        $exception = null;
        try {
            $hydrator->create(Car::class);
        } catch (Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(DependencyNotFoundException::class, $exception);
        $this->assertSame(
            'Dependency "Yiisoft\Hydrator\Tests\Support\Classes\EngineInterface" not resolved.',
            $exception->getMessage()
        );
        $this->assertSame(
            'Dependency not resolved.',
            $exception->getName()
        );
        $this->assertMatchesRegularExpression(
            '~Dependency "Yiisoft\\\Hydrator\\\Tests\\\Support\\\Classes\\\EngineInterface" not resolved because~',
            $exception->getSolution()
        );
    }

    public function testHas(): void
    {
        $container = new EmptyDependencyContainer();

        $this->assertFalse($container->has('anything'));
    }
}
