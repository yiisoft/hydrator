<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionClass;
use Yiisoft\Hydrator\Attribute\SkipHydration;

class ConstructorArgumentsExtractor
{
    public ParameterAttributesHandler $parameterAttributesHandler;
    public TypeCasterInterface $typeCaster;
    private DataAttributesHandler $dataAttributesHandler;

    public function __construct(
        DataAttributesHandler $dataAttributesHandler,
        ParameterAttributesHandler $parameterAttributesHandler,
        TypeCasterInterface $typeCaster,
    )
    {
        $this->dataAttributesHandler = $dataAttributesHandler;
        $this->parameterAttributesHandler = $parameterAttributesHandler;
        $this->typeCaster = $typeCaster;
    }

    /**
     * @psalm-param class-string $class
     * @psalm-param MapType $map
     * @psalm-return array{0:list<string>,1:array<string,mixed>}
     */
    public function getConstructorArguments(string $class, array $sourceData, array $map, bool $strict): array
    {
        $excludeParameterNames = [];
        $constructorArguments = [];

        $constructor = (new ReflectionClass($class))->getConstructor();
        if ($constructor === null) {
            return [$excludeParameterNames, $constructorArguments];
        }

        $data = $this->createData($class, $sourceData, $map, $strict);

        foreach ($constructor->getParameters() as $parameter) {
            if (!empty($parameter->getAttributes(SkipHydration::class))) {
                continue;
            }

            $parameterName = $parameter->getName();
            $resolveResult = Result::fail();

            if ($parameter->isPromoted()) {
                $excludeParameterNames[] = $parameterName;
                $resolveResult = $this->resolve($parameterName, $data);
            }

            $attributesHandleResult = $this->parameterAttributesHandler->handle(
                $parameter,
                $resolveResult,
                $data
            );
            if ($attributesHandleResult->isResolved()) {
                $resolveResult = $attributesHandleResult;
            }

            if ($resolveResult->isResolved()) {
                $typeCastedValue = $this->typeCaster->cast(
                    $resolveResult->getValue(),
                    $parameter->getType()
                );
                if ($typeCastedValue->isResolved()) {
                    $constructorArguments[$parameterName] = $typeCastedValue->getValue();
                }
            }
        }

        return [$excludeParameterNames, $constructorArguments];
    }


    private function resolve(string $name, Data $data): Result
    {
        $map = $data->getMap();

        if ($data->isStrict() && !array_key_exists($name, $map)) {
            return Result::fail();
        }

        return DataHelper::getValueByPath($data->getData(), $map[$name] ?? $name);
    }

    /**
     * @psalm-param object|class-string $object
     * @psalm-param MapType $map
     */
    private function createData(object|string $object, array $sourceData, array $map, bool $strict): Data
    {
        $data = new Data($sourceData, $map, $strict);

        $attributes = (new ReflectionClass($object))
            ->getAttributes(DataAttributeInterface::class, \ReflectionAttribute::IS_INSTANCEOF);

        $this->dataAttributesHandler->handle($attributes, $data);

        return $data;
    }
}
