<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionClass;
use ReflectionProperty;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\AttributeResolverFactoryInterface;
use Yiisoft\Hydrator\AttributeHandling\DataAttributesHandler;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\Exception\NonExistClassException;
use Yiisoft\Hydrator\Internal\ConstructorArgumentsExtractor;
use Yiisoft\Hydrator\Internal\ReflectionFilter;
use Yiisoft\Hydrator\ObjectFactory\ObjectFactoryInterface;
use Yiisoft\Hydrator\ObjectFactory\ReflectionObjectFactory;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ReflectionAttributeResolverFactory;
use Yiisoft\Hydrator\TypeCaster\CompositeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;

/**
 * Creates or hydrate objects from a set of raw data.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
final class Hydrator implements HydratorInterface
{
    /**
     * @var TypeCasterInterface Type caster used to cast raw values.
     */
    private TypeCasterInterface $typeCaster;

    private ObjectFactoryInterface $objectFactory;

    private DataAttributesHandler $dataAttributesHandler;

    private ParameterAttributesHandler $parameterAttributesHandler;

    private ConstructorArgumentsExtractor $constructorArgumentsExtractor;

    /**
     * @param TypeCasterInterface|null $typeCaster Type caster used to cast raw values.
     */
    public function __construct(
        ?TypeCasterInterface $typeCaster = null,
        ?AttributeResolverFactoryInterface $attributeResolverFactory = null,
        ?ObjectFactoryInterface $objectFactory = null,
    ) {
        $this->typeCaster = $typeCaster ?? new CompositeTypeCaster(
            new PhpNativeTypeCaster(),
            new HydratorTypeCaster(),
        );

        $attributeResolverFactory ??= new ReflectionAttributeResolverFactory();
        $this->dataAttributesHandler = new DataAttributesHandler($attributeResolverFactory);
        $this->parameterAttributesHandler = new ParameterAttributesHandler($attributeResolverFactory);

        $this->objectFactory = $objectFactory ?? new ReflectionObjectFactory();

        $this->constructorArgumentsExtractor = new ConstructorArgumentsExtractor(
            $this,
            $this->parameterAttributesHandler,
            $this->typeCaster,
        );
    }

    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void
    {
        $dataObject = new Data($data, $map, $strict);
        $reflectionClass = new ReflectionClass($object);

        $this->dataAttributesHandler->handle($reflectionClass, $dataObject);

        $this->hydrateInternal(
            $object,
            ReflectionFilter::filterProperties($reflectionClass),
            $dataObject
        );
    }

    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object
    {
        if (!class_exists($class)) {
            throw new NonExistClassException($class);
        }

        $reflectionClass = new ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();

        $dataObject = new Data($data, $map, $strict);
        $this->dataAttributesHandler->handle($reflectionClass, $dataObject);

        [$excludeProperties, $constructorArguments] = $this->constructorArgumentsExtractor->extract(
            $constructor,
            $dataObject,
        );

        $object = $this->objectFactory->create($reflectionClass, $constructorArguments);

        $this->hydrateInternal(
            $object,
            ReflectionFilter::filterProperties($reflectionClass, $excludeProperties),
            $dataObject
        );

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
