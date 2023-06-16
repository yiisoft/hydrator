<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Hydrator\ResolverInitiator\NonInitiableException;
use Yiisoft\Injector\Injector;

final class ObjectInitiator
{
    /**
     * @param ContainerInterface $container Container to get attributes' resolvers from.
     */
    public function __construct(
        private ?Injector $injector = null,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return object
     */
    public function initiate(\ReflectionClass $reflectionClass, array $constructorArguments): object
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
