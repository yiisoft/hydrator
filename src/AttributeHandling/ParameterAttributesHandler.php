<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeHandling;

use ReflectionAttribute;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\AttributeResolverFactoryInterface;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Data;
use Yiisoft\Hydrator\Result;

/**
 * Handles parameters attributes that implement {@see ParameterAttributeInterface}.
 */
final class ParameterAttributesHandler
{
    public function __construct(
        private AttributeResolverFactoryInterface $attributeResolverFactory,
    ) {
    }

    /**
     * Handle parameters' attributes of passed parameter.
     *
     * @param ReflectionParameter|ReflectionProperty $parameter Parameter or property reflection to handle attributes
     * from.
     * @param Result|null $resolveResult The resolved value object to pass to attribute resolver via {@see ParameterAttributeResolveContext}.
     * @param Data|null $data Raw data and map to pass to attribute resolver via {@see ParameterAttributeResolveContext}.
     *
     * @return Result The resolved from attributes value object.
     */
    public function handle(
        ReflectionParameter|ReflectionProperty $parameter,
        ?Result $resolveResult = null,
        ?Data $data = null
    ): Result {
        $resolveResult ??= Result::fail();

        $reflectionAttributes = $parameter
            ->getAttributes(ParameterAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);

        $hereResolveResult = Result::fail();
        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();

            $resolver = $this->attributeResolverFactory->create($attribute);
            if (!$resolver instanceof ParameterAttributeResolverInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Parameter attribute resolver "%s" must implement "%s".',
                        get_debug_type($resolver),
                        ParameterAttributeResolverInterface::class,
                    ),
                );
            }

            $context = new ParameterAttributeResolveContext(
                $parameter,
                $hereResolveResult->isResolved() ? $hereResolveResult : $resolveResult,
                $data?->getData() ?? [],
                $data?->getMap() ?? [],
            );

            $hereResolveResult = $resolver->getParameterValue($attribute, $context);
        }

        return $hereResolveResult;
    }
}
