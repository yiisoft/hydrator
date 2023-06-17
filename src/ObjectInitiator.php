<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use Yiisoft\Hydrator\ResolverInitiator\NonInitiableException;
use Yiisoft\Injector\Injector;

final class ObjectInitiator
{
    public function __construct(
        private ?Injector $injector = null,
    ) {
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     *
     * @psalm-template T of object
     * @psalm-param ReflectionClass<T> $reflectionClass
     * @psalm-return T
     */
    public function initiate(ReflectionClass $reflectionClass, array $constructorArguments): object
    {
        $class = $reflectionClass->getName();
        if ($this->injector !== null) {
            return $this->injector->make($class, $constructorArguments);
        }

        $constructorReflection = $reflectionClass->getConstructor();
        if ($constructorReflection === null ||
            $constructorReflection->getNumberOfRequiredParameters() <= count($constructorArguments)
        ) {
            return $reflectionClass->newInstanceArgs($constructorArguments);
        }
        throw new NonInitiableException(
            sprintf(
                'Class "%s" cannot be initiated because it has required %d parameters in constructor, but passed %d.',
                $class,
                $constructorReflection->getNumberOfRequiredParameters(),
                count($constructorArguments)
            )
        );
    }
}
