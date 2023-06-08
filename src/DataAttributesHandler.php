<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionAttribute;
use Yiisoft\Hydrator\Initiator\AttributeResolverInitiator;

/**
 * Handles data attributes that implement {@see DataAttributeInterface}.
 *
 * @internal
 */
final class DataAttributesHandler
{
    /**
     * @param ContainerInterface $container Container to get attributes' resolvers from.
     */
    public function __construct(
        private AttributeResolverInitiator $attributeResolverInitiator,
    ) {
    }

    /**
     * Handle data attributes.
     *
     * @param ReflectionAttribute[] $reflectionAttributes Reflections of attributes to handle.
     * @param Data $data Current {@see Data} object.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @psalm-param ReflectionAttribute<DataAttributeInterface>[] $reflectionAttributes
     */
    public function handle(array $reflectionAttributes, Data $data): void
    {
        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();
            $resolver = $this->attributeResolverInitiator->initiate($attribute->getResolver());
            $resolver->prepareData($attribute, $data);
        }
    }
}
