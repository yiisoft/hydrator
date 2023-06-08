<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionAttribute;
use ReflectionParameter;
use ReflectionProperty;
use Yiisoft\Hydrator\Initiator\AttributeResolverInitiator;

/**
 * Handles parameters attributes that implement {@see ParameterAttributeInterface}.
 */
final class ParameterAttributesHandler
{
    /**
     * @param ContainerInterface $container Container to get attributes' resolvers from.
     * @param TypeCasterInterface|null $typeCaster Type caster used to cast values.
     */
    public function __construct(
        private AttributeResolverInitiator $attributeResolverInitiator,
        private ?TypeCasterInterface $typeCaster = null,
    ) {
    }

    /**
     * Handle parameters' attributes of passed parameter.
     *
     * @param ReflectionParameter|ReflectionProperty $parameter Parameter or property reflection to handle attributes
     * from.
     * @param Result|null $resolveResult The resolved value object to pass to attribute resolver via {@see Context}.
     * @param Data|null $data Raw data and map to pass to attribute resolver via {@see Context}.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
            $resolver = $this->attributeResolverInitiator->initiate($attribute->getResolver());

            $context = new Context(
                $parameter,
                $hereResolveResult->isResolved() ? $hereResolveResult : $resolveResult,
                $data?->getData() ?? [],
                $data?->getMap() ?? [],
            );

            $hereResolveResult = $resolver->getParameterValue($attribute, $context);
        }

        if ($this->typeCaster !== null && $hereResolveResult->isResolved()) {
            $result = $this->typeCaster->cast($hereResolveResult->getValue(), $parameter->getType());
            if ($result->isResolved()) {
                $hereResolveResult = $result;
            }
        }

        return $hereResolveResult;
    }
}
