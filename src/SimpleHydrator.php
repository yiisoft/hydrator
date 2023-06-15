<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Closure;
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
final class SimpleHydrator implements HydratorInterface
{
    public ConstructorArgumentsExtractor $constructorArgumentsExtractor;
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
    public function __construct(?TypeCasterInterface $typeCaster = null, ?AttributeResolverInitiator $initiator = null)
    {
        $initiator ??= new AttributeResolverInitiator();

        $this->typeCaster = $typeCaster ?? (new SimpleTypeCaster())->withHydrator($this);
        $this->dataAttributesHandler = new DataAttributesHandler($initiator);
        $this->parameterAttributesHandler = new ParameterAttributesHandler($initiator);
        $this->objectPropertiesExtractor = new ObjectPropertiesExtractor();
        $this->dataPropertyAccessor = new DataPropertyAccessor();
        $this->constructorArgumentsExtractor = new ConstructorArgumentsExtractor(
            $this->dataAttributesHandler,
            $this->parameterAttributesHandler,
            $this->typeCaster,
            $this->objectPropertiesExtractor,
            $this->dataPropertyAccessor,
        );
    }

    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void
    {
        $values = $this->getHydrateData($object, $data, $map, $strict, []);
        $this->populate(
            $object,
            $values,
        );
    }

    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object
    {
        if (!class_exists($class)) {
            throw new NonInitiableException();
        }
        [$excludeProperties, $constructorArguments] = $this->constructorArgumentsExtractor->getConstructorArguments(
            $class,
            $data,
            $map,
            $strict
        );

        $reflection = new \ReflectionClass($class);
        $constructorReflection = $reflection->getConstructor();
        if ($constructorReflection && $constructorReflection->getNumberOfRequiredParameters() > count(
                $constructorArguments
            )) {
            throw new NonInitiableException();
        }
        $object = new $class(...$constructorArguments);

        $values = $this->getHydrateData($object, $data, $map, $strict, $excludeProperties);

        $this->populate(
            $object,
            $values,
        );

        return $object;
    }

    /**
     * @psalm-param MapType $map
     */
    private function getHydrateData(
        object $object,
        array $sourceData,
        array $map,
        bool $strict,
        array $excludeProperties,
    ): array {
        $hydrateData = [];

        $data = $this->createData($object, $sourceData, $map, $strict);

        $properties = (new ReflectionClass($object))->getProperties();
        $reflectionProperties = $this->objectPropertiesExtractor->filterReflectionProperties($properties);
        foreach ($reflectionProperties as $property) {
            $propertyName = $property->getName();
            if (in_array($propertyName, $excludeProperties, true)) {
                continue;
            }

            $resolveResult = $this->dataPropertyAccessor->resolve($propertyName, $data);


            $attributesHandleResult = $this->parameterAttributesHandler->handle($property, $resolveResult, $data);
            if ($attributesHandleResult->isResolved()) {
                $resolveResult = $attributesHandleResult;
            }

            if ($resolveResult->isResolved()) {
                $result = $this->typeCaster->cast($resolveResult->getValue(), $property->getType());
                if ($result->isResolved()) {
                    $hydrateData[$propertyName] = $result->getValue();
                }
            }
        }

        return $hydrateData;
    }

    private function populate(object $object, array $values): void
    {
        /** @var Closure $setter */
        $setter = Closure::bind(
            static function (object $object, string $propertyName, mixed $value): void {
                $object->$propertyName = $value;
            },
            null,
            $object
        );

        foreach ($values as $propertyName => $value) {
            $setter($object, $propertyName, $value);
        }
    }

    /**
     * @psalm-param object|class-string $object
     * @psalm-param MapType $map
     */
    private function createData(object|string $object, array $sourceData, array $map, bool $strict): Data
    {
        $data = new Data($sourceData, $map, $strict);

        $attributes = (new ReflectionClass($object))
            ->getAttributes(DataAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);

        $this->dataAttributesHandler->handle($attributes, $data);

        return $data;
    }
}
