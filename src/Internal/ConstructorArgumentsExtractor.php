<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Internal;

use ReflectionClass;
use Yiisoft\Hydrator\Data;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;

/**
 * @internal
 */
final class ConstructorArgumentsExtractor
{
    public function __construct(
        private Hydrator $hydrator,
        private ParameterAttributesHandler $parameterAttributesHandler,
        private TypeCasterInterface $typeCaster,
    ) {
    }

    /**
     * @psalm-return array{0:list<string>,1:array<string,mixed>}
     */
    public function extract(ReflectionClass $reflectionClass, Data $data): array
    {
        $excludeParameterNames = [];
        $constructorArguments = [];

        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return [$excludeParameterNames, $constructorArguments];
        }

        $reflectionParameters = ReflectionFilter::filterParameters($constructor->getParameters());

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
                    new TypeCastContext($this->hydrator, $parameter),
                );
                if ($typeCastedValue->isResolved()) {
                    $constructorArguments[$parameterName] = $typeCastedValue->getValue();
                }
            }
        }

        return [$excludeParameterNames, $constructorArguments];
    }
}
