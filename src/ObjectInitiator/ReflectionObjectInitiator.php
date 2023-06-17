<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ObjectInitiator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use Yiisoft\Hydrator\ResolverInitiator\NonInitiableException;

final class ReflectionObjectInitiator implements ObjectInitiatorInterface
{
    /**
     * @psalm-template T
     * @psalm-param ReflectionClass<T> $reflectionClass
     * @psalm-return T
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function initiate(ReflectionClass $reflectionClass, array $constructorArguments): object
    {
        $constructorReflection = $reflectionClass->getConstructor();
        if ($constructorReflection !== null &&
            $constructorReflection->getNumberOfRequiredParameters() > count($constructorArguments)
        ) {
            throw new NonInitiableException(
                sprintf(
                    'Class "%s" cannot be initiated because it has %d required parameters in constructor, but passed only %d.',
                    $reflectionClass->getName(),
                    $constructorReflection->getNumberOfRequiredParameters(),
                    count($constructorArguments),
                )
            );
        }
        return $reflectionClass->newInstanceArgs($constructorArguments);
    }
}
