<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionAttribute;
use RuntimeException;
use Yiisoft\Hydrator\AttributeResolverInitiator\AttributeResolverInitiatorInterface;
use Yiisoft\Hydrator\AttributeResolverInitiator\NonInitiableResolverException;

/**
 * Handles data attributes that implement {@see DataAttributeInterface}.
 *
 * @internal
 */
final class DataAttributesHandler
{
    public function __construct(
        private AttributeResolverInitiatorInterface $attributeResolverInitiator,
    ) {
    }

    /**
     * Handle data attributes.
     *
     * @param ReflectionAttribute[] $reflectionAttributes Reflections of attributes to handle.
     * @param Data $data Current {@see Data} object.
     *
     * @throws NonInitiableResolverException
     *
     * @psalm-param ReflectionAttribute<DataAttributeInterface>[] $reflectionAttributes
     */
    public function handle(array $reflectionAttributes, Data $data): void
    {
        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();

            $resolver = $this->attributeResolverInitiator->initiate($attribute);
            if (!$resolver instanceof DataAttributeResolverInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Data attribute resolver "%s" must implement "%s".',
                        get_debug_type($resolver),
                        DataAttributeResolverInterface::class,
                    )
                );
            }

            $resolver->prepareData($attribute, $data);
        }
    }
}
