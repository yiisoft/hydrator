<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Internal;

use ReflectionClass;
use ReflectionMethod;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\DataInterface;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ObjectFactory\ObjectFactoryInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;

/**
 * @internal
 */
final class ObjectFactoryWithConstructor implements InternalObjectFactoryInterface
{
    public function __construct(
        private readonly ObjectFactoryInterface $objectFactory,
        private readonly Hydrator $hydrator,
        private readonly ParameterAttributesHandler $parameterAttributesHandler,
        private readonly TypeCasterInterface $typeCaster,
    ) {
    }

    public function create(ReflectionClass $reflectionClass, DataInterface $data): array
    {
        [$excludeProperties, $constructorArguments] = $this->extract(
            $reflectionClass->getConstructor(),
            $data,
        );
        $object = $this->objectFactory->create($reflectionClass, $constructorArguments);
        return [$object, $excludeProperties];
    }

    /**
     * @psalm-return array{0:list<string>,1:array<string,mixed>}
     */
    public function extract(?ReflectionMethod $constructor, DataInterface $data): array
    {
        $excludeParameterNames = [];
        $constructorArguments = [];

        if ($constructor === null) {
            return [$excludeParameterNames, $constructorArguments];
        }

        $reflectionParameters = ReflectionFilter::filterParameters($constructor->getParameters());

        foreach ($reflectionParameters as $parameterName => $parameter) {
            $resolveResult = Result::fail();

            if ($parameter->isPromoted()) {
                $excludeParameterNames[] = $parameterName;
                $resolveResult = $data->getValue($parameterName);
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
