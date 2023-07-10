<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionClass;

/**
 * @internal
 */
final class ConstructorArgumentsExtractor
{
    public function __construct(private ParameterAttributesHandler $parameterAttributesHandler, private TypeCasterInterface $typeCaster, private ObjectPropertiesExtractor $objectPropertiesExtractor)
    {
    }

    /**
     * @psalm-return array{0:list<string>,1:array<string,mixed>}
     */
    public function getConstructorArguments(ReflectionClass $reflectionClass, Data $data): array
    {
        $excludeParameterNames = [];
        $constructorArguments = [];

        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return [$excludeParameterNames, $constructorArguments];
        }

        $reflectionParameters = $this->objectPropertiesExtractor->filterReflectionParameters($constructor->getParameters());

        foreach ($reflectionParameters as $parameter) {
            $parameterName = $parameter->getName();
            $resolveResult = Result::fail();

            if ($parameter->isPromoted()) {
                $excludeParameterNames[] = $parameterName;
                $resolveResult = $data->resolveValue($parameterName);
            }

            $attributesHandleResult = $this->parameterAttributesHandler->handle(
                $parameter,
                $resolveResult,
                $data,
            );
            if ($attributesHandleResult->isResolved()) {
                $resolveResult = $attributesHandleResult;
            }

            if ($resolveResult->isResolved()) {
                $typeCastedValue = $this->typeCaster->cast(
                    $resolveResult->getValue(),
                    $parameter->getType(),
                );
                if ($typeCastedValue->isResolved()) {
                    $constructorArguments[$parameterName] = $typeCastedValue->getValue();
                }
            }
        }

        return [$excludeParameterNames, $constructorArguments];
    }
}
