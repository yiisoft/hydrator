<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\AttributeHandling\ResolverFactory;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\AttributeHandling\Exception\AttributeResolverNonInstantiableException;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ReflectionAttributeResolverFactory;
use Yiisoft\Hydrator\Tests\Support\Attribute\AbstractResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\CustomResolverAttr;
use Yiisoft\Hydrator\Tests\Support\Attribute\CustomValue;
use Yiisoft\Hydrator\Tests\Support\Attribute\ParameterizedResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\PrivateConstructorResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\ResolverWithConstructorWithoutParameters;

final class ReflectionAttributeResolverFactoryTest extends TestCase
{
    public function testBase(): void
    {
        $attribute = new Counter('test');
        $factory = new ReflectionAttributeResolverFactory();

        $result = $factory->create($attribute);

        $this->assertInstanceOf(CounterResolver::class, $result);
    }

    public function testResolverIsAttributeItself(): void
    {
        $attribute = new CustomValue('test');
        $factory = new ReflectionAttributeResolverFactory();

        $result = $factory->create($attribute);

        $this->assertSame($attribute, $result);
    }

    public function testNonExistClass(): void
    {
        $attribute = new CustomResolverAttr('NonExistClass');
        $factory = new ReflectionAttributeResolverFactory();

        $this->expectException(AttributeResolverNonInstantiableException::class);
        $this->expectExceptionMessage('Class "NonExistClass" does not exist.');
        $factory->create($attribute);
    }

    public function testAbstractClass(): void
    {
        $attribute = new CustomResolverAttr(AbstractResolver::class);
        $factory = new ReflectionAttributeResolverFactory();

        $this->expectException(AttributeResolverNonInstantiableException::class);
        $this->expectExceptionMessage('"' . AbstractResolver::class . '" is not instantiable because it is abstract.');
        $factory->create($attribute);
    }

    public function testNonPublicConstructor(): void
    {
        $attribute = new CustomResolverAttr(PrivateConstructorResolver::class);
        $factory = new ReflectionAttributeResolverFactory();

        $this->expectException(AttributeResolverNonInstantiableException::class);
        $this->expectExceptionMessage(
            'Class "' . PrivateConstructorResolver::class . '" is not instantiable because of non-public constructor.'
        );
        $factory->create($attribute);
    }

    public function testConstructorWithParameters(): void
    {
        $attribute = new CustomResolverAttr(ParameterizedResolver::class);
        $factory = new ReflectionAttributeResolverFactory();

        $this->expectException(AttributeResolverNonInstantiableException::class);
        $this->expectExceptionMessage(
            'Class "' . ParameterizedResolver::class . '" cannot be instantiated because it has 1 required parameters in constructor.'
        );
        $factory->create($attribute);
    }

    public function testConstructorWithoutParameters(): void
    {
        $attribute = new CustomResolverAttr(ResolverWithConstructorWithoutParameters::class);
        $factory = new ReflectionAttributeResolverFactory();

        $resolver = $factory->create($attribute);

        $this->assertInstanceOf(ResolverWithConstructorWithoutParameters::class, $resolver);
    }
}
