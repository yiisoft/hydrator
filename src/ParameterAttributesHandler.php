<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;

use function is_string;

final class ParameterAttributesHandler
{
    public function __construct(
        private ContainerInterface $container,
        private ?TypeCasterInterface $typeCaster = null,
    ) {
    }

    /**
     * @throws NotResolvedException
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

            $result = $this->typeCaster->cast($resolvedValue, $parameter->getType());
            return $result->isCasted() ? $result->getValue() : $resolvedValue;
        }

        throw new NotResolvedException();
    }

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
