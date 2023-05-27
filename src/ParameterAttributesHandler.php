<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;

use function is_string;

/**
 * Handles parameters and attributes.
 */
final class ParameterAttributesHandler
{
    /**
     * @param ContainerInterface $container DI container to get resolvers from.
     * @param TypeCasterInterface|null $typeCaster Type caster to use to cast values.
     */
    public function __construct(
        private ContainerInterface $container,
        private ?TypeCasterInterface $typeCaster = null,
    ) {
    }

    /**
     * Handle resolving.
     *
     * @param ReflectionParameter|ReflectionProperty $parameter Parameter or property to resolve.
     * @param Value $resolvedValue The resolved value object.
     * @param Data|null $data Data to be used for resolving.
     *
     * @return Value The resolved value object.
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
            $resolver = $this->getParameterResolver($attribute);

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

    /**
     * @param ParameterAttributeInterface $attribute The attribute to be resolved.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return ParameterAttributeResolverInterface The parameter attribute resolver.
     */
    private function getParameterResolver(ParameterAttributeInterface $attribute): ParameterAttributeResolverInterface
    {
        $resolver = $attribute->getResolver();
        if (is_string($resolver)) {
            $resolver = $this->container->get($resolver);
            if (!$resolver instanceof ParameterAttributeResolverInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Parameter attribute resolver "%1$s" must implement "%2$s".',
                        $resolver::class,
                        ParameterAttributeResolverInterface::class,
                    )
                );
            }
        }

        return $resolver;
    }
}
