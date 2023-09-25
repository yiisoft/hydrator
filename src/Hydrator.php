<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionAttribute;
use ReflectionClass;
use Yiisoft\Hydrator\Exception\NonInstantiableException;
use Yiisoft\Hydrator\ObjectFactory\ObjectFactoryInterface;
use Yiisoft\Hydrator\ObjectFactory\ReflectionObjectFactory;
use Yiisoft\Hydrator\ResolverFactory\AttributeResolverFactoryInterface;
use Yiisoft\Hydrator\ResolverFactory\ReflectionAttributeResolverFactory;
use Yiisoft\Hydrator\TypeCaster\CompositeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;

/**
 * Creates or hydrate objects from a set of raw data.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
final class Hydrator implements HydratorInterface
{
    private ConstructorArgumentsExtractor $constructorArgumentsExtractor;
    private ObjectFactoryInterface $objectFactory;
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
    private ObjectPropertiesFilter $objectPropertiesFilter;

    /**
     * @param TypeCasterInterface|null $typeCaster Type caster used to cast raw values.
     */
    public function __construct(
        ?TypeCasterInterface $typeCaster = null,
        ?AttributeResolverFactoryInterface $attributeResolverFactory = null,
        ?ObjectFactoryInterface $objectFactory = null,
    ) {
        $this->objectFactory = $objectFactory ?? new ReflectionObjectFactory();
        $attributeResolverFactory ??= new ReflectionAttributeResolverFactory();
        $this->setTypeCaster($typeCaster);
        $this->dataAttributesHandler = new DataAttributesHandler($attributeResolverFactory);
        $this->parameterAttributesHandler = new ParameterAttributesHandler($attributeResolverFactory);
        $this->objectPropertiesFilter = new ObjectPropertiesFilter();
        $this->constructorArgumentsExtractor = new ConstructorArgumentsExtractor(
            $this->parameterAttributesHandler,
            $this->typeCaster,
            $this->objectPropertiesFilter,
        );
    }

    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void
    {
        $reflectionClass = new ReflectionClass($object);
        $data = $this->createData($data, $map, $strict);
        $this->handleDataAttributes($reflectionClass, $data);

        $reflectionProperties = $this->objectPropertiesFilter->filterReflectionProperties(
            $reflectionClass->getProperties(),
            []
        );
        $this->hydrateInternal($object, $reflectionProperties, $data);
    }

    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object
    {
        if (!class_exists($class)) {
            throw new NonInstantiableException();
        }
        $reflectionClass = new ReflectionClass($class);
        $data = $this->createData($data, $map, $strict);
        $this->handleDataAttributes($reflectionClass, $data);

        [$excludeProperties, $constructorArguments] = $this->constructorArgumentsExtractor->extract(
            $reflectionClass,
            $data,
        );

        $reflectionProperties = $this->objectPropertiesFilter->filterReflectionProperties(
            $reflectionClass->getProperties(),
            $excludeProperties
        );

        $object = $this->objectFactory->create($reflectionClass, $constructorArguments);
        $this->hydrateInternal($object, $reflectionProperties, $data);

        return $object;
    }

    /**
     * @param array<string, \ReflectionProperty> $reflectionProperties
     * @psalm-param MapType $map
     */
    private function hydrateInternal(
        object $object,
        array $reflectionProperties,
        Data $data,
    ): void {
        foreach ($reflectionProperties as $propertyName => $property) {
            $resolveResult = $data->resolveValue($propertyName);

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
                    if (PHP_VERSION_ID < 80100) {
                        $property->setAccessible(true);
                    }
                    $property->setValue($object, $result->getValue());
                }
            }
        }
    }

    /**
     * @psalm-param MapType $map
     */
    private function createData(array $sourceData, array $map, bool $strict): Data
    {
        return new Data($sourceData, $map, $strict);
    }

    private function handleDataAttributes(ReflectionClass $reflectionClass, Data $data): void
    {
        $attributes = $reflectionClass->getAttributes(
            DataAttributeInterface::class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        $this->dataAttributesHandler->handle($attributes, $data);
    }

    private function setTypeCaster(?TypeCasterInterface $typeCaster): void
    {
        $typeCaster = $typeCaster ?? new CompositeTypeCaster(
            new PhpNativeTypeCaster(),
            new HydratorTypeCaster(),
        );

        if ($typeCaster instanceof TypeCasterWithHydratorInterface) {
            $typeCaster->setHydrator($this);
        }

        $this->typeCaster = $typeCaster;
    }
}
