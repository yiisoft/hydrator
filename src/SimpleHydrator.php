<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Closure;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Yiisoft\Hydrator\Attribute\SkipHydration;
use Yiisoft\Hydrator\Initiator\AttributeResolverInitiator;
use Yiisoft\Hydrator\Initiator\NonInitiableException;
use Yiisoft\Hydrator\TypeCaster\SimpleTypeCaster;

use function array_key_exists;

/**
 * Creates or hydrate objects from a set of raw data.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
final class SimpleHydrator implements HydratorInterface
{
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

    /**
     * @param TypeCasterInterface|null $typeCaster Type caster used to cast raw values.
     */
    public function __construct(?TypeCasterInterface $typeCaster = null, ?AttributeResolverInitiator $initiator = null)
    {
        $initiator ??= new AttributeResolverInitiator();

        $this->typeCaster = $typeCaster ?? (new SimpleTypeCaster())->withHydrator($this);
        $this->dataAttributesHandler = new DataAttributesHandler($initiator);
        $this->parameterAttributesHandler = new ParameterAttributesHandler($initiator);
    }

    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void
    {
        $this->populate(
            $object,
            $this->getHydrateData($object, $data, $map, $strict),
        );
    }

    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object
    {
        if (!class_exists($class)) {
            throw new NonInitiableException();
        }

        $reflection = new \ReflectionClass($class);
        $constructorReflection = $reflection->getConstructor();
        if ($constructorReflection->getNumberOfRequiredParameters() > 0) {
            throw new NonInitiableException();
        }
        $object = new $class();

        $this->populate(
            $object,
            $this->getHydrateData($object, $data, $map, $strict),
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
    ): array {
        $hydrateData = [];

        $data = $this->createData($object, $sourceData, $map, $strict);

        foreach ($this->getObjectProperties($object) as $property) {
            if (!empty($property->getAttributes(SkipHydration::class))) {
                continue;
            }

            $propertyName = $property->getName();

            $resolveResult = $this->resolve($propertyName, $data);

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

    private function resolve(string $name, Data $data): Result
    {
        $map = $data->getMap();

        if ($data->isStrict() && !array_key_exists($name, $map)) {
            return Result::fail();
        }

        return DataHelper::getValueByPath($data->getData(), $map[$name] ?? $name);
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
     * @psalm-return array<string, ReflectionProperty>
     */
    private function getObjectProperties(object $object): array
    {
        $result = [];

        $properties = (new ReflectionClass($object))->getProperties();
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            /** @psalm-suppress UndefinedMethod Need for PHP 8.0 only */
            if (PHP_VERSION_ID >= 80100 && $property->isReadOnly()) {
                continue;
            }

            $result[$property->getName()] = $property;
        }

        return $result;
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
