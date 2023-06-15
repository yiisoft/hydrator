<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionClass;
use Yiisoft\Hydrator\TypeCaster\SimpleTypeCaster;
use Yiisoft\Injector\Injector;

/**
 * Creates or hydrate objects from a set of raw data.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
final class Hydrator implements HydratorInterface
{
    private ConstructorArgumentsExtractor $constructorArgumentsExtractor;

    public function __construct(
        private HydratorInterface $decoratedHydrator,
        private Injector $injector,
        /**
         * @var TypeCasterInterface Type caster used to cast raw values.
         */
        TypeCasterInterface $typeCaster,

        /**
         * @var DataAttributesHandler Data attributes handler.
         */
        private DataAttributesHandler $dataAttributesHandler,

        /**
         * @var ParameterAttributesHandler Parameter attributes handler.
         */
        ParameterAttributesHandler $parameterAttributesHandler,
    ) {
        $typeCaster = $typeCaster instanceof SimpleTypeCaster ? $typeCaster->withHydrator($this) : $typeCaster;
        $this->constructorArgumentsExtractor = new ConstructorArgumentsExtractor(
            $parameterAttributesHandler,
            $typeCaster,
            new ObjectPropertiesExtractor(),
            new DataPropertyAccessor(),
        );
    }

    public function hydrate(object $object, array $data = [], array $map = [], bool $strict = false): void
    {
        $this->decoratedHydrator->hydrate($object, $data, $map, $strict);
    }

    public function create(string $class, array $data = [], array $map = [], bool $strict = false): object
    {
        [$excludeProperties, $constructorArguments] = $this->constructorArgumentsExtractor->getConstructorArguments(
            $class,
            $this->createData($class, $data, $map, $strict),
        );

        $object = $this->injector->make($class, $constructorArguments);
        // todo handle $excludeProperties

        $this->decoratedHydrator->hydrate(
            $object,
            $data,
            $map,
            $strict
        );

        return $object;
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
