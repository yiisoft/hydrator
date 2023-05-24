<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\NotResolvedException;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;

final class DiResolver implements ParameterAttributeResolverInterface
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws DiNotFoundException
     * @throws NotResolvedException
     */
    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed
    {
        if (!$attribute instanceof Di) {
            throw new UnexpectedAttributeException(Di::class, $attribute);
        }

        $id = $attribute->getId();
        if ($id !== null) {
            return $this->container->get($id);
        }

        $parameter = $context->getParameter();
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType) {
            if (!$type->isBuiltin()) {
                try {
                    return $this->container->get($type->getName());
                } catch (NotFoundExceptionInterface $e) {
                    throw $this->hasDefaultValue($parameter)
                        ? new NotResolvedException()
                        : new DiNotFoundException($parameter, $e);
                }
            }
        } elseif ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $type) {
                /** @psalm-suppress RedundantConditionGivenDocblockType Need for PHP less than 8.2 */
                if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                    try {
                        return $this->container->get($type->getName());
                    } catch (NotFoundExceptionInterface) {
                    }
                }
            }
        }

        throw $this->hasDefaultValue($parameter)
            ? new NotResolvedException()
            : new DiNotFoundException($parameter);
    }

    private function hasDefaultValue(ReflectionParameter|ReflectionProperty $reflection): bool
    {
        return $reflection instanceof ReflectionParameter
            ? $reflection->isDefaultValueAvailable()
            : $reflection->hasDefaultValue();
    }
}
