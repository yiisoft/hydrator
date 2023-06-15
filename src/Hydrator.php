<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

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
        DataAttributesHandler $dataAttributesHandler,

        /**
         * @var ParameterAttributesHandler Parameter attributes handler.
         */
        ParameterAttributesHandler $parameterAttributesHandler,
    ) {
        $typeCaster = $typeCaster instanceof SimpleTypeCaster ? $typeCaster->withHydrator($this) : $typeCaster;
        $this->constructorArgumentsExtractor = new ConstructorArgumentsExtractor(
            $dataAttributesHandler,
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
            $data,
            $map,
            $strict
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
}
