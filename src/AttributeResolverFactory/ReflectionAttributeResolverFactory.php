<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeResolverFactory;

use ReflectionClass;
use ReflectionException;
use Yiisoft\Hydrator\AttributeResolverFactoryInterface;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\Exception\NonInstantiableException;
use Yiisoft\Hydrator\ObjectFactory\ReflectionObjectFactory;
use Yiisoft\Hydrator\ParameterAttributeInterface;

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
