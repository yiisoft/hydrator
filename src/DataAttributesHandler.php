<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionAttribute;
use ReflectionClass;
use RuntimeException;
use Yiisoft\Hydrator\Exception\NonInstantiableException;
use Yiisoft\Hydrator\ResolverFactory\AttributeResolverFactoryInterface;

/**
 * Handles data attributes that implement {@see DataAttributeInterface}.
 *
 * @internal
 */
final class DataAttributesHandler
{
    public function __construct(
        private AttributeResolverFactoryInterface $attributeResolverFactory,
    ) {
    }

    /**
     * Handle data attributes.
     *
     * @param ReflectionClass $reflectionClass Reflection of class to attributes handle.
     * @param Data $data Current {@see Data} object.
     *
     * @throws NonInstantiableException
     *
     * @psalm-param ReflectionAttribute<DataAttributeInterface>[] $reflectionAttributes
     */
    public function handle(ReflectionClass $reflectionClass, Data $data): void
    {
        $reflectionAttributes = $reflectionClass->getAttributes(
            DataAttributeInterface::class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();

            $resolver = $this->attributeResolverFactory->create($attribute);
            if (!$resolver instanceof DataAttributeResolverInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Data attribute resolver "%s" must implement "%s".',
                        get_debug_type($resolver),
                        DataAttributeResolverInterface::class,
                    ),
                );
            }

            $resolver->prepareData($attribute, $data);
        }
    }
}
