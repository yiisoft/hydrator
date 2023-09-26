<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Yiisoft\Hydrator\Exception\NonInstantiableException;
use Yiisoft\Hydrator\Internal\ConstructorArgumentsExtractor;
use Yiisoft\Hydrator\DataAttributesHandler;
use Yiisoft\Hydrator\Internal\ObjectPropertiesFilter;
use Yiisoft\Hydrator\ParameterAttributesHandler;
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
    private DataAttributesHandler $dataAttributesHandler;
    private ParameterAttributesHandler $parameterAttributesHandler;
    private ObjectPropertiesFilter $objectPropertiesFilter;

    private ObjectFactoryInterface $objectFactory;
    /**
     * @var TypeCasterInterface Type caster used to cast raw values.
     */
    private TypeCasterInterface $typeCaster;

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

        $this->typeCaster = $typeCaster ?? new CompositeTypeCaster(
            new PhpNativeTypeCaster(),
            new HydratorTypeCaster(),
        );

        $this->dataAttributesHandler = new DataAttributesHandler($attributeResolverFactory);
        $this->parameterAttributesHandler = new ParameterAttributesHandler($attributeResolverFactory);
        $this->objectPropertiesFilter = new ObjectPropertiesFilter();
        $this->constructorArgumentsExtractor = new ConstructorArgumentsExtractor(
            $this,
            $this->parameterAttributesHandler,
            $this->typeCaster,
            $this->objectPropertiesFilter,
        );
    }

    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void
    {
        $dataObject = new Data($data, $map, $strict);
        $reflectionClass = new ReflectionClass($object);
        $this->dataAttributesHandler->handle($reflectionClass, $dataObject);

        $reflectionProperties = $this->objectPropertiesFilter->filterReflectionProperties(
            $reflectionClass->getProperties(),
            []
        );
        $this->hydrateInternal($object, $reflectionProperties, $dataObject);
    }

    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object
    {
        if (!class_exists($class)) {
            throw new NonInstantiableException();
        }

        $dataObject = new Data($data, $map, $strict);
        $reflectionClass = new ReflectionClass($class);
        $this->dataAttributesHandler->handle($reflectionClass, $dataObject);

        [$excludeProperties, $constructorArguments] = $this->constructorArgumentsExtractor->extract(
            $reflectionClass,
            $dataObject,
        );

        $reflectionProperties = $this->objectPropertiesFilter->filterReflectionProperties(
            $reflectionClass->getProperties(),
            $excludeProperties
        );

        $object = $this->objectFactory->create($reflectionClass, $constructorArguments);
        $this->hydrateInternal($object, $reflectionProperties, $dataObject);

        return $object;
    }

    /**
     * @param array<string, ReflectionProperty> $reflectionProperties
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
                    new TypeCastContext($this, $property),
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
}
