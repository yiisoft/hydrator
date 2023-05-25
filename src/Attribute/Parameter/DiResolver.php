<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionNamedType;
use ReflectionUnionType;
use Yiisoft\Hydrator\Context;
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
     */
    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed
    {
        if (!$attribute instanceof Di) {
            throw new UnexpectedAttributeException(Di::class, $attribute);
        }

        $parameter = $context->getParameter();

        $id = $attribute->getId();
        if ($id !== null) {
            try {
                return $this->container->get($id);
            } catch (NotFoundExceptionInterface $e) {
                throw new DiNotFoundException($parameter, $e);
            }
        }

        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType) {
            if (!$type->isBuiltin()) {
                try {
                    return $this->container->get($type->getName());
                } catch (NotFoundExceptionInterface $e) {
                    throw new DiNotFoundException($parameter, $e);
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

        throw new DiNotFoundException($parameter);
    }
}
