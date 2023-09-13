<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverFactory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\Exception\NonInstantiableException;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;

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
     * @throws NotFoundExceptionInterface
     */
    public function create(DataAttributeInterface|ParameterAttributeInterface $attribute): object
    {
        $resolver = $this->getResolver($attribute);

        if ($attribute instanceof DataAttributeInterface) {
            if (!$resolver instanceof DataAttributeResolverInterface) {
                throw new \RuntimeException(
                    sprintf(
                        'Data attribute resolver "%s" must implement "%s".',
                        get_debug_type($resolver),
                        DataAttributeResolverInterface::class,
                    ),
                );
            }
        } else {
            if (!$resolver instanceof ParameterAttributeResolverInterface) {
                throw new \RuntimeException(
                    sprintf(
                        'Parameter attribute resolver "%s" must implement "%s".',
                        get_debug_type($resolver),
                        ParameterAttributeResolverInterface::class,
                    ),
                );
            }
        }
        return $resolver;
    }

    private function getResolver(DataAttributeInterface|ParameterAttributeInterface $attribute): mixed
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
            throw new \RuntimeException(
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
