<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverInitiator;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;

use function is_string;

final class ReflectionAttributeResolverInitiator implements AttributeResolverInitiatorInterface
{
    /**
     * @throws NonInitiableException
     */
    public function initiate(DataAttributeInterface|ParameterAttributeInterface $attribute): object
    {
        $resolver = $attribute->getResolver();
        if (!is_string($resolver)) {
            return $resolver;
        }

        if (!class_exists($resolver)) {
            throw new NonInitiableException(
                sprintf(
                    'Class "%s" does not exist.',
                    $resolver,
                ),
            );
        }

        $reflection = new \ReflectionClass($resolver);
        $constructorReflection = $reflection->getConstructor();
        if ($constructorReflection && $constructorReflection->getNumberOfRequiredParameters() > 0) {
            throw new NonInitiableException(
                sprintf(
                    'Class "%s" has constructor with %d required parameters.',
                    $resolver,
                    $constructorReflection->getNumberOfRequiredParameters(),
                ),
            );
        }


        return $reflection->newInstance();
    }
}
