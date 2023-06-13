<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverInitiator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;

use function is_string;

final class AttributeResolverInitiator
{
    /**
     * @param ContainerInterface $container Container to get attributes' resolvers from.
     */
    public function __construct(
        private ?ContainerInterface $container = null,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return DataAttributeResolverInterface|mixed|object|string
     */
    public function initiate(string|object $resolver): object
    {
        if (!is_string($resolver)) {
            return $resolver;
        }

        if ($this->container !== null && $this->container->has($resolver)) {
            return $this->container->get($resolver);
        }

        if (!class_exists($resolver)) {
            throw new NonInitiableException();
        }

        $reflection = new \ReflectionClass($resolver);
        $constructorReflection = $reflection->getConstructor();
        if ($constructorReflection && $constructorReflection->getNumberOfRequiredParameters() > 0) {
            throw new NonInitiableException();
        }


        $resolver1 = new $resolver();
        if (!$resolver1 instanceof ParameterAttributeResolverInterface) {
            throw new \RuntimeException('Parameter attribute resolver "' .
                $resolver .
                '" must implement "' .
                ParameterAttributeResolverInterface::class . '".');
        }
        return $resolver1;
    }
}
