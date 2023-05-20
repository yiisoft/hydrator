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
     * @param bool $resolved Whether the parameter or property is resolved.
     * @param mixed $resolvedValue The resolved value.
     * @param Data|null $data Data to be used for resolving.
     *
     * @throws NotResolvedException
     * @return mixed The resolved value.
     */
    public function handle(
        ReflectionParameter|ReflectionProperty $parameter,
        bool $resolved = false,
        mixed $resolvedValue = null,
        ?Data $data = null
    ): mixed {
        $reflectionAttributes = $parameter
            ->getAttributes(ParameterAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);

        $hereResolved = false;
        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();
            $resolver = $this->getParameterResolver($attribute);

            $context = new Context(
                $parameter,
                $resolved || $hereResolved,
                $resolvedValue,
                $data?->getData() ?? [],
                $data?->getMap() ?? [],
            );

            try {
                $resolvedValue = $resolver->getParameterValue($attribute, $context);
                $hereResolved = true;
            } catch (NotResolvedException) {
            }
        }

        if ($hereResolved) {
            if ($this->typeCaster === null) {
                return $resolvedValue;
            }
            try {
                return $this->typeCaster->cast($resolvedValue, $parameter->getType());
            } catch (SkipTypeCastException) {
                return $resolvedValue;
            }
        }

        throw new NotResolvedException();
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
