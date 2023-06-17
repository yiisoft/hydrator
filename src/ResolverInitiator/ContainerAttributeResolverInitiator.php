<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverInitiator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;

use function is_string;

final class ContainerAttributeResolverInitiator implements AttributeResolverInitiatorInterface
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
     * @throws NotFoundExceptionInterface
     * @return DataAttributeResolverInterface|mixed|object|string
     */
    public function initiate(DataAttributeInterface|ParameterAttributeInterface $attribute): object
    {
        $resolver = $attribute->getResolver();
        if (!is_string($resolver)) {
            return $resolver;
        }

        if (!$this->container->has($resolver)) {
            throw new NonInitiableException(
                sprintf(
                    'Class "%s" does not exist.',
                    $resolver,
                ),
            );
        }
        return $this->container->get($resolver);
    }
}
