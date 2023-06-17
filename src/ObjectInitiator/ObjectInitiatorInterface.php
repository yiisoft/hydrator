<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ObjectInitiator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;

interface ObjectInitiatorInterface
{
    /**
     * @psalm-template T of object
     * @psalm-param ReflectionClass<T> $reflectionClass
     * @psalm-return T
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function initiate(ReflectionClass $reflectionClass, array $constructorArguments): object;
}
