<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Container;

use LogicException;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class AttributeResolverNotFoundException extends LogicException implements NotFoundExceptionInterface, FriendlyExceptionInterface
{
    public function __construct(
        private string $id,
    ) {
        parent::__construct(
            sprintf('Attribute resolver "%s" not found.', $id)
        );
    }

    public function getName(): string
    {
        return 'Attribute resolver not found.';
    }

    public function getSolution(): ?string
    {
        return <<<SOLUTION
            You use attribute with separate resolver "$this->id", but attribute resolvers container not configured in
            hydrator. Without it available use only simple attributes such as `Map`, `Data` or `ToString`.

            Define `attributeResolverContainer` constructor parameter of `Hydrator` in configuration:

            ```php
            use Psr\Container\ContainerInterface;
            use Yiisoft\Definitions\Reference;
            use Yiisoft\Hydrator\Hydrator;
            use Yiisoft\Hydrator\HydratorInterface;

            return [
                HydratorInterface::class => [
                    'class' => Hydrator::class,
                    '__construct()' => [
                        'attributeResolverContainer' => Reference::to(ContainerInterface::class),
                    ],
                ],
            ];
            ```

            Or configure attribute resolvers container in hydrator constructor if use it directly:

            ```php
            new Hydrator(
                attributeResolverContainer: \$container,
            );
            ```
            SOLUTION;
    }
}
