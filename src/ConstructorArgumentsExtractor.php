<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionClass;

class ConstructorArgumentsExtractor
{
    public ParameterAttributesHandler $parameterAttributesHandler;
    public TypeCasterInterface $typeCaster;
    public ObjectPropertiesExtractor $objectPropertiesExtractor;
    public DataPropertyAccessor $dataPropertyAccessor;
    private DataAttributesHandler $dataAttributesHandler;

    public function __construct(
        DataAttributesHandler $dataAttributesHandler,
        ParameterAttributesHandler $parameterAttributesHandler,
        TypeCasterInterface $typeCaster,
        ObjectPropertiesExtractor $objectPropertiesExtractor,
        DataPropertyAccessor $dataPropertyAccessor
    )
    {
        $this->dataAttributesHandler = $dataAttributesHandler;
        $this->parameterAttributesHandler = $parameterAttributesHandler;
        $this->typeCaster = $typeCaster;
        $this->objectPropertiesExtractor = $objectPropertiesExtractor;
        $this->dataPropertyAccessor = $dataPropertyAccessor;
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

        $reflectionParameters = $this->objectPropertiesExtractor->filterReflectionParameters($constructor->getParameters());

        foreach ($reflectionParameters as $parameter) {
            $parameterName = $parameter->getName();
            $resolveResult = Result::fail();

            if ($parameter->isPromoted()) {
                $excludeParameterNames[] = $parameterName;
                $resolveResult = $this->dataPropertyAccessor->resolve($parameterName, $data);
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
