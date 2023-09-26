<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeHandling\ResolverFactory;

use ReflectionClass;
use ReflectionException;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\AttributeResolverFactoryInterface;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;
use Yiisoft\Hydrator\NonInstantiableException;
use Yiisoft\Hydrator\ObjectFactory\ReflectionObjectFactory;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;

use function is_string;

final class ReflectionAttributeResolverFactory implements AttributeResolverFactoryInterface
{
    private ReflectionObjectFactory $objectFactory;

    public function __construct()
    {
        $this->objectFactory = new ReflectionObjectFactory();
    }

    /**
     * @throws NonInstantiableException
     * @throws ReflectionException
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
        $reflectionClass = new ReflectionClass($resolver);

        return $this->objectFactory->create($reflectionClass, []);
    }
}
