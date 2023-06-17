<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverInitiator;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\ObjectInitiator\ObjectInitiatorInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;

use function is_string;

final class ReflectionAttributeResolverInitiator implements AttributeResolverInitiatorInterface
{
    public function __construct(
        private ObjectInitiatorInterface $objectInitiator,
    ) {
    }

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
        $reflectionClass = new \ReflectionClass($resolver);

        return $this->objectInitiator->initiate($reflectionClass, []);
    }
}
