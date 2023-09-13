<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\ResolverFactory;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\Exception\NonInstantiableException;
use Yiisoft\Hydrator\ObjectFactory\ObjectFactoryInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;

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
        $resolver = $this->getResolver($attribute);

        if ($attribute instanceof DataAttributeInterface) {
            if (!$resolver instanceof DataAttributeResolverInterface) {
                throw new \RuntimeException(
                    sprintf(
                        'Data attribute resolver "%s" must implement "%s".',
                        get_debug_type($resolver),
                        DataAttributeResolverInterface::class,
                    ),
                );
            }
        } else {
            if (!$resolver instanceof ParameterAttributeResolverInterface) {
                throw new \RuntimeException(
                    sprintf(
                        'Parameter attribute resolver "%s" must implement "%s".',
                        get_debug_type($resolver),
                        ParameterAttributeResolverInterface::class,
                    ),
                );
            }
        }

        return $resolver;
    }

    private function getResolver(DataAttributeInterface|ParameterAttributeInterface $attribute): object
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
