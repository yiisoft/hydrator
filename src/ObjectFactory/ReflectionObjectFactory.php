<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ObjectFactory;

use ReflectionClass;
use ReflectionException;
use Yiisoft\Hydrator\Exception\NonInstantiableException;

use function count;

final class ReflectionObjectFactory implements ObjectFactoryInterface
{
    /**
     * @psalm-template T of object
     * @psalm-param ReflectionClass<T> $reflectionClass
     * @psalm-return T
     *
     * @throws NonInstantiableException
     * @throws ReflectionException
     */
    public function create(ReflectionClass $reflectionClass, array $constructorArguments): object
    {
        $constructorReflection = $reflectionClass->getConstructor();
        if ($constructorReflection !== null &&
            $constructorReflection->getNumberOfRequiredParameters() > count($constructorArguments)
        ) {
            throw new NonInstantiableException(
                sprintf(
                    'Class "%s" cannot be instantiated because it has %d required parameters in constructor, but passed only %d.',
                    $reflectionClass->getName(),
                    $constructorReflection->getNumberOfRequiredParameters(),
                    count($constructorArguments),
                )
            );
        }
        return $reflectionClass->newInstance(...$constructorArguments);
    }
}
