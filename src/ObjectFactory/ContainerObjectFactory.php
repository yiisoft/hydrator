<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ObjectFactory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use Yiisoft\Injector\Injector;

final class ContainerObjectFactory implements ObjectFactoryInterface
{
    public function __construct(
        private Injector $injector,
    ) {
    }

    /**
     * @psalm-template T of object
     * @psalm-param ReflectionClass<T> $reflectionClass
     * @psalm-return T
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function create(ReflectionClass $reflectionClass, array $constructorArguments): object
    {
        $class = $reflectionClass->getName();
        return $this->injector->make($class, $constructorArguments);
    }
}