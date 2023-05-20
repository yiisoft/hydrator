<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use RuntimeException;

use function is_string;

/**
 * Handles data attributes.
 *
 * @internal
 */
final class DataAttributesHandler
{
    /**
     * @param ContainerInterface $container Container to use for getting data resolvers.
     */
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @param ReflectionAttribute[] $reflectionAttributes Reflections of attributes to handle.
     * @psalm-param ReflectionAttribute<DataAttributeInterface>[] $reflectionAttributes
     * @param Data $data Data to handle attributes for.
     */
    public function handle(array $reflectionAttributes, Data $data): void
    {
        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();
            $this->getDataResolver($attribute)->prepareData($attribute, $data);
        }
    }

    /**
     * Get data attribute resolver.
     *
     * @param DataAttributeInterface $attribute Data attribute to get resolver for.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return DataAttributeResolverInterface Resolver for the attribute.
     */
    private function getDataResolver(DataAttributeInterface $attribute): DataAttributeResolverInterface
    {
        $resolver = $attribute->getResolver();
        if (is_string($resolver)) {
            $resolver = $this->container->get($resolver);
            if (!$resolver instanceof DataAttributeResolverInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Data attribute resolver "%s" must implement "%s".',
                        $resolver::class,
                        DataAttributeResolverInterface::class,
                    )
                );
            }
        }

        return $resolver;
    }
}
