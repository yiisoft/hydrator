<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use RuntimeException;

use function is_string;

/**
 * @internal
 */
final class DataAttributesHandler
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @param ReflectionAttribute[] $reflectionAttributes
     *
     * @psalm-param ReflectionAttribute<DataAttributeInterface>[] $reflectionAttributes
     */
    public function handle(array $reflectionAttributes, Data $data): void
    {
        foreach ($reflectionAttributes as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();
            $this->getDataResolver($attribute)->prepareData($attribute, $data);
        }
    }

    private function getDataResolver(DataAttributeInterface $attribute): DataAttributeResolverInterface
    {
        $resolver = $attribute->getResolver();
        if (is_string($resolver)) {
            $resolver = $this->container->get($resolver);
            if (!$resolver instanceof DataAttributeResolverInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Data attribute resolver "%s" must implement "%s".',
                        $resolver::class,
                        DataAttributeResolverInterface::class,
                    )
                );
            }
        }

        return $resolver;
    }
}
