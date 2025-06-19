<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Internal;

use ReflectionClass;
use ReflectionProperty;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\AttributeHandling\DataAttributesHandler;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\DataInterface;
use Yiisoft\Hydrator\Exception\NonExistClassException;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;

use function is_array;

/**
 * @internal
 */
final class InternalHydrator
{
    public function __construct(
        private readonly TypeCasterInterface $typeCaster,
        private readonly DataAttributesHandler $dataAttributesHandler,
        private readonly ParameterAttributesHandler $parameterAttributesHandler,
        private readonly InternalObjectFactoryInterface $objectFactory,
        private readonly HydratorInterface $hydrator,
    ) {
    }

    public function hydrate(object $object, array|DataInterface $data): void
    {
        if (is_array($data)) {
            $data = new ArrayData($data);
        }

        $reflectionClass = new ReflectionClass($object);

        $data = $this->dataAttributesHandler->handle($reflectionClass, $data);

        $this->hydrateInternal(
            $object,
            $reflectionClass,
            ReflectionFilter::filterProperties($object, $reflectionClass),
            $data
        );
    }

    /**
     * @psalm-template T as object
     * @psalm-param class-string<T> $class
     * @psalm-return T
     */
    public function create(string $class, array|DataInterface $data = []): object
    {
        if (!class_exists($class)) {
            throw new NonExistClassException($class);
        }

        if (is_array($data)) {
            $data = new ArrayData($data);
        }

        $reflectionClass = new ReflectionClass($class);

        $data = $this->dataAttributesHandler->handle($reflectionClass, $data);

        [$object, $excludeProperties] = $this->objectFactory->create($reflectionClass, $data);

        $this->hydrateInternal(
            $object,
            $reflectionClass,
            ReflectionFilter::filterProperties($object, $reflectionClass, $excludeProperties),
            $data
        );

        return $object;
    }

    /**
     * @param array<string, ReflectionProperty> $reflectionProperties
     */
    private function hydrateInternal(
        object $object,
        ReflectionClass $reflectionClass,
        array $reflectionProperties,
        DataInterface $data,
    ): void {
        foreach ($reflectionProperties as $propertyName => $property) {
            $resolveResult = $data->getValue($propertyName);

            $attributesHandleResult = $this->parameterAttributesHandler->handle(
                $property,
                $resolveResult,
                $data,
            );
            if ($attributesHandleResult->isResolved()) {
                $resolveResult = $attributesHandleResult;
            }

            if ($resolveResult->isResolved()) {
                $result = $this->typeCaster->cast(
                    $resolveResult->getValue(),
                    new TypeCastContext($this->hydrator, $property),
                );
                if ($result->isResolved()) {
                    $this
                        ->preparePropertyToSetValue($reflectionClass, $property, $propertyName)
                        ->setValue($object, $result->getValue());
                }
            }
        }
    }

    private function preparePropertyToSetValue(
        ReflectionClass $class,
        ReflectionProperty $property,
        string $propertyName,
    ): ReflectionProperty {
        if ($property->isReadOnly()) {
            $declaringClass = $property->getDeclaringClass();
            if ($declaringClass !== $class) {
                return $declaringClass->getProperty($propertyName);
            }
        }
        return $property;
    }
}
