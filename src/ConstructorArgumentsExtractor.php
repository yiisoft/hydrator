<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionClass;

/**
 * @internal
 */
final class ConstructorArgumentsExtractor
{
    public function __construct(
        private ParameterAttributesHandler $parameterAttributesHandler,
        private TypeCasterInterface $typeCaster,
        private ObjectPropertiesFilter $objectPropertiesFilter
    ) {
    }

    /**
     * @psalm-return array{0:list<string>,1:array<string,mixed>}
     */
    public function extract(ReflectionClass $reflectionClass, Data $data, TypeCastContext $typeCastContext): array
    {
        $excludeParameterNames = [];
        $constructorArguments = [];

        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return [$excludeParameterNames, $constructorArguments];
        }

        $reflectionParameters = $this->objectPropertiesFilter->filterReflectionParameters($constructor->getParameters());

        foreach ($reflectionParameters as $parameterName => $parameter) {
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
                    $typeCastContext->withItem($parameter),
                );
                if ($typeCastedValue->isResolved()) {
                    $constructorArguments[$parameterName] = $typeCastedValue->getValue();
                }
            }
        }

        return [$excludeParameterNames, $constructorArguments];
    }
}
