<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Psr\Container\ContainerInterface;
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

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed
    {
        if (!$attribute instanceof Di) {
            throw new UnexpectedAttributeException(Di::class, $attribute);
        }

        return $this->container->get($attribute->getId());
    }
}
