<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Container;

use LogicException;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class DependencyNotFoundException extends LogicException implements NotFoundExceptionInterface, FriendlyExceptionInterface
{
    public function __construct(
        private string $id,
    ) {
        parent::__construct(
            sprintf('Dependency "%s" not resolved.', $id)
        );
    }

    public function getName(): string
    {
        return 'Dependency not resolved.';
    }

    public function getSolution(): ?string
    {
        return <<<SOLUTION
            Dependency "$this->id" not resolved because dependency container not configured in hydrator. Without it,
            dependencies resolving on create objects is not available.

            Define `dependencyContainer` constructor parameter of `Hydrator` in configuration:

            ```php
            use Psr\Container\ContainerInterface;
            use Yiisoft\Definitions\Reference;
            use Yiisoft\Hydrator\Hydrator;
            use Yiisoft\Hydrator\HydratorInterface;

            return [
                HydratorInterface::class => [
                    'class' => Hydrator::class,
                    '__construct()' => [
                        'dependencyContainer' => Reference::to(ContainerInterface::class),
                    ],
                ],
            ];
            ```

            Or configure dependency container in hydrator constructor if use it directly:

            ```php
            new Hydrator(
                dependencyContainer: \$container,
            );
            ```
            SOLUTION;
    }
}
