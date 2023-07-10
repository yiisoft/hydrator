<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ObjectFactory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;

interface ObjectFactoryInterface
{
    /**
     * @psalm-template T of object
     * @psalm-param ReflectionClass<T> $reflectionClass
     * @psalm-return T
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function create(ReflectionClass $reflectionClass, array $constructorArguments): object;
}
