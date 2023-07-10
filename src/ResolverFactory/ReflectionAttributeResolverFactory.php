<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverFactory;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\ObjectFactory\ObjectFactoryInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;

use function is_string;

final class ReflectionAttributeResolverFactory implements AttributeResolverFactoryInterface
{
    public function __construct(
        private ObjectFactoryInterface $objectFactory,
    ) {
    }

    /**
     * @throws NonInstantiableException
     */
    public function create(DataAttributeInterface|ParameterAttributeInterface $attribute): object
    {
        $resolver = $attribute->getResolver();
        if (!is_string($resolver)) {
            return $resolver;
        }

        if (!class_exists($resolver)) {
            throw new NonInstantiableException(
                sprintf(
                    'Class "%s" does not exist.',
                    $resolver,
                ),
            );
        }
        $reflectionClass = new \ReflectionClass($resolver);

        return $this->objectFactory->create($reflectionClass, []);
    }
}
