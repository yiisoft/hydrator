<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ObjectFactory;

use ReflectionClass;
use Yiisoft\Hydrator\Exception\NonInstantiableException;

/**
 * An interface for object factory.
 */
interface ObjectFactoryInterface
{
    /**
     * Creates an object of the given class.
     *
     * Callers must not pass enums to this method, so implementations aren't required to handle them.
     *
     * @throws NonInstantiableException
     *
     * @psalm-template T of object
     * @psalm-param ReflectionClass<T> $reflectionClass
     * @psalm-return T
     */
    public function create(ReflectionClass $reflectionClass, array $constructorArguments): object;
}
