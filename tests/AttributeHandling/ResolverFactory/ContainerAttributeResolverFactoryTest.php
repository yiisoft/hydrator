<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\AttributeHandling\ResolverFactory;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\AttributeHandling\Exception\AttributeResolverNonInstantiableException;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\CustomValue;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ContainerAttributeResolverFactoryTest extends TestCase
{
    public function testBase(): void
    {
        $attribute = new Counter('test');
        $resolver = new CounterResolver();
        $container = new SimpleContainer([CounterResolver::class => $resolver]);
        $factory = new ContainerAttributeResolverFactory($container);

        $result = $factory->create($attribute);

        $this->assertSame($resolver, $result);
    }

    public function testResolverIsAttributeItself(): void
    {
        $attribute = new CustomValue('test');
        $container = new SimpleContainer();
        $factory = new ContainerAttributeResolverFactory($container);

        $result = $factory->create($attribute);

        $this->assertSame($attribute, $result);
    }

    public function testResolverNotExists(): void
    {
        $attribute = new Counter('test');
        $container = new SimpleContainer();
        $factory = new ContainerAttributeResolverFactory($container);

        $this->expectException(AttributeResolverNonInstantiableException::class);
        $this->expectExceptionMessage('Class "' . CounterResolver::class . '" does not exist.');
        $factory->create($attribute);
    }
}
