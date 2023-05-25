<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Yiisoft\Hydrator\Attribute\SkipHydration;
use Yiisoft\Hydrator\TypeCaster\SimpleTypeCaster;
use Yiisoft\Injector\Injector;

use function array_key_exists;
use function in_array;

/**
 * @psalm-import-type MapType from HydratorInterface
 */
final class Hydrator implements HydratorInterface
{
    private Injector $injector;
    private TypeCasterInterface $typeCaster;
    private DataAttributesHandler $dataAttributesHandler;
    private ParameterAttributesHandler $parameterAttributesHandler;

    public function __construct(
        ContainerInterface $container,
        ?TypeCasterInterface $typeCaster = null,
    ) {
        $this->injector = new Injector($container);
        $this->typeCaster = $typeCaster ?? (new SimpleTypeCaster())->withHydrator($this);
        $this->dataAttributesHandler = new DataAttributesHandler($container);
        $this->parameterAttributesHandler = new ParameterAttributesHandler($container);
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
        [$excludeProperties, $constructorArguments] = $this->getConstructorArguments($class, $data, $map, $strict);

        $object = $this->injector->make($class, $constructorArguments);

        $this->populate(
            $object,
            $this->getHydrateData($object, $data, $map, $strict, $excludeProperties),
        );

        return $object;
    }

    /**
     * @psalm-param class-string $class
     * @psalm-param MapType $map
     * @psalm-return array{0:list<string>,1:array<string,mixed>}
     */
    private function getConstructorArguments(string $class, array $sourceData, array $map, bool $strict): array
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
            $resolvedValue = Value::fail();

            if ($parameter->isPromoted()) {
                $excludeParameterNames[] = $parameterName;
                $resolvedValue = $this->resolve($parameterName, $data);
            }

            $valueFromAttributes = $this->parameterAttributesHandler->handle(
                $parameter,
                $resolvedValue,
                $data
            );
            if ($valueFromAttributes->exist()) {
                $resolvedValue = $valueFromAttributes;
            }

            if ($resolvedValue->exist()) {
                $typeCastResult = $this->typeCaster->cast(
                    $resolvedValue->getValue(),
                    $parameter->getType()
                );
                if ($typeCastResult->isCasted()) {
                    $constructorArguments[$parameterName] = $typeCastResult->getValue();
                }
            }
        }

        return [$excludeParameterNames, $constructorArguments];
    }

    /**
     * @psalm-param MapType $map
     */
    private function getHydrateData(
        object $object,
        array $sourceData,
        array $map,
        bool $strict,
        array $excludeProperties = []
    ): array {
        $hydrateData = [];

        $data = $this->createData($object, $sourceData, $map, $strict);

        foreach ($this->getObjectProperties($object) as $property) {
            if (!empty($property->getAttributes(SkipHydration::class))) {
                continue;
            }

            $propertyName = $property->getName();
            if (in_array($propertyName, $excludeProperties, true)) {
                continue;
            }

            $resolvedValue = $this->resolve($propertyName, $data);

            $valueFromAttributes = $this->parameterAttributesHandler->handle(
                $property,
                $resolvedValue,
                $data
            );
            if ($valueFromAttributes->exist()) {
                $resolvedValue = $valueFromAttributes;
            }

            if ($resolvedValue->exist()) {
                $typeCastResult = $this->typeCaster->cast($resolvedValue->getValue(), $property->getType());
                if ($typeCastResult->isCasted()) {
                    $hydrateData[$propertyName] = $typeCastResult->getValue();
                }
            }
        }

        return $hydrateData;
    }

    private function resolve(string $name, Data $data): Value
    {
        $map = $data->getMap();

        if ($data->isStrict() && !array_key_exists($name, $map)) {
            return Value::fail();
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
