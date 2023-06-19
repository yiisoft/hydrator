<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeResolverInitiator;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;

use function is_string;

final class SimpleAttributeResolverInitiator implements AttributeResolverInitiatorInterface
{
    /**
     * @throws NonInitiableResolverException
     */
    public function initiate(DataAttributeInterface|ParameterAttributeInterface $attribute): object
    {
        $resolver = $attribute->getResolver();
        if (!is_string($resolver)) {
            return $resolver;
        }

        if (!class_exists($resolver)) {
            throw new NonInitiableResolverException(
                sprintf(
                    'Class "%s" does not exist.',
                    $resolver,
                ),
            );
        }

        /** @psalm-suppress MixedMethodCall */
        return new $resolver();
    }
}
