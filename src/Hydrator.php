<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionAttribute;
use ReflectionClass;
use Yiisoft\Hydrator\ResolverInitiator\AttributeResolverInitiator;
use Yiisoft\Hydrator\ResolverInitiator\NonInitiableException;
use Yiisoft\Hydrator\TypeCaster\SimpleTypeCaster;

/**
 * Creates or hydrate objects from a set of raw data.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
final class Hydrator implements HydratorInterface
{
    public ConstructorArgumentsExtractor $constructorArgumentsExtractor;
    public ?ObjectInitiator $objectInitiator;
    /**
     * @var TypeCasterInterface Type caster used to cast raw values.
     */
    private TypeCasterInterface $typeCaster;

    /**
     * @var DataAttributesHandler Data attributes handler.
     */
    private DataAttributesHandler $dataAttributesHandler;

    /**
     * @var ParameterAttributesHandler Parameter attributes handler.
     */
    private ParameterAttributesHandler $parameterAttributesHandler;
    private ObjectPropertiesExtractor $objectPropertiesExtractor;
    private DataPropertyAccessor $dataPropertyAccessor;

    /**
     * @param TypeCasterInterface|null $typeCaster Type caster used to cast raw values.
     */
    public function __construct(
        ?TypeCasterInterface $typeCaster = null,
        ?AttributeResolverInitiator $initiator = null,
        ?ObjectInitiator $objectInitiator = null,
    ) {
        $initiator ??= new AttributeResolverInitiator();

        $this->typeCaster = $typeCaster ?? (new SimpleTypeCaster())->withHydrator($this);
        $this->dataAttributesHandler = new DataAttributesHandler($initiator);
        $this->parameterAttributesHandler = new ParameterAttributesHandler($initiator);
        $this->objectPropertiesExtractor = new ObjectPropertiesExtractor();
        $this->dataPropertyAccessor = new DataPropertyAccessor();
        $this->constructorArgumentsExtractor = new ConstructorArgumentsExtractor(
            $this->parameterAttributesHandler,
            $this->typeCaster,
            $this->objectPropertiesExtractor,
            $this->dataPropertyAccessor,
        );
        $this->objectInitiator = $objectInitiator ?? new ObjectInitiator();
    }

    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void
    {
        $reflectionClass = new \ReflectionClass($object);
        $data = $this->createData($reflectionClass, $data, $map, $strict);
        $reflectionProperties = $this->getFilterReflectionProperties($reflectionClass, []);
        $values = $this->getHydrateData($reflectionProperties, $data);
        $this->populate(
            $object,
            $values,
            $reflectionProperties,
        );
    }

    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object
    {
        if (!class_exists($class)) {
            throw new NonInitiableException();
        }
        $reflectionClass = new \ReflectionClass($class);
        $data = $this->createData($reflectionClass, $data, $map, $strict);
        [$excludeProperties, $constructorArguments] = $this->constructorArgumentsExtractor->getConstructorArguments(
            $reflectionClass,
            $data,
        );

        $object = $this->objectInitiator->initiate($reflectionClass, $constructorArguments);

        $reflectionProperties = $this->getFilterReflectionProperties($reflectionClass, $excludeProperties);
        $values = $this->getHydrateData($reflectionProperties, $data);

        $this->populate(
            $object,
            $values,
            $reflectionProperties,
        );

        return $object;
    }

    /**
     * @psalm-param MapType $map
     */
    private function getHydrateData(
        array $reflectionProperties,
        Data $data,
    ): array {
        $hydrateData = [];

        foreach ($reflectionProperties as $property) {
            $propertyName = $property->getName();

            $resolveResult = $this->dataPropertyAccessor->resolve($propertyName, $data);

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
                    $property->getType(),
                );
                if ($result->isResolved()) {
                    $hydrateData[$propertyName] = $result->getValue();
                }
            }
        }

        return $hydrateData;
    }

    /**
     * @param \ReflectionProperty[] $reflectionProperties
     */
    private function populate(object $object, array $values, array $reflectionProperties): void
    {
        foreach ($values as $propertyName => $value) {
            $parameter = $reflectionProperties[$propertyName];
            if ($parameter !== null) {
                $parameter->setValue($object, $values[$parameter->getName()]);
            }
        }
    }

    /**
     * @psalm-param object|class-string $object
     * @psalm-param MapType $map
     */
    private function createData($reflectionClass, array $sourceData, array $map, bool $strict): Data
    {
        $data = new Data($sourceData, $map, $strict);

        $attributes = $reflectionClass->getAttributes(
            DataAttributeInterface::class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        $this->dataAttributesHandler->handle($attributes, $data);

        return $data;
    }

    protected function getFilterReflectionProperties(ReflectionClass $reflectionClass, array $excludeProperties): array
    {
        $properties = $reflectionClass->getProperties();
        return $this->objectPropertiesExtractor->filterReflectionProperties(
            $properties,
            $excludeProperties
        );
    }
}
