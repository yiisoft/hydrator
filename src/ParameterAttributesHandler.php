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

    public function handle(
        ReflectionParameter|ReflectionProperty $parameter,
        ?Value $resolvedValue = null,
        ?Data $data = null
    ): Value {
        $resolvedValue ??= Value::fail();

        $reflectionAttributes = $parameter
            ->getAttributes(ParameterAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);

        $hereResolvedValue = Value::fail();
        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();
            $resolver = $this->getParameterResolver($attribute);

            $context = new Context(
                $parameter,
                $hereResolvedValue->exist() ? $hereResolvedValue : $resolvedValue,
                $data?->getData() ?? [],
                $data?->getMap() ?? [],
            );

            $hereResolvedValue = $resolver->getParameterValue($attribute, $context);
        }

        if ($this->typeCaster !== null && $hereResolvedValue->exist()) {
            $typeCastResult = $this->typeCaster->cast($hereResolvedValue->getValue(), $parameter->getType());
            if ($typeCastResult->isCasted()) {
                $hereResolvedValue = Value::success($typeCastResult->getValue());
            }
        }

        return $hereResolvedValue;
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
