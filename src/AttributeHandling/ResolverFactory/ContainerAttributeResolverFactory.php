<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeHandling\ResolverFactory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;
use Yiisoft\Hydrator\NonInstantiableException;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;

use function is_object;
use function is_string;

final class ContainerAttributeResolverFactory implements AttributeResolverFactoryInterface
{
    /**
     * @param ContainerInterface $container Container to get attributes' resolvers from.
     */
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NonInstantiableException
     */
    public function create(DataAttributeInterface|ParameterAttributeInterface $attribute): object
    {
        $resolver = $attribute->getResolver();
        if (!is_string($resolver)) {
            return $resolver;
        }

        if (!$this->container->has($resolver)) {
            throw new NonInstantiableException(
                sprintf(
                    'Class "%s" does not exist.',
                    $resolver,
                ),
            );
        }
        $result = $this->container->get($resolver);

        if (!is_object($result)) {
            throw new RuntimeException(
                sprintf(
                    'Resolver "%s" must be an object, "%s" given.',
                    $resolver,
                    get_debug_type($result),
                ),
            );
        }

        return $result;
    }
}
