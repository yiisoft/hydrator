<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Container;

use Psr\Container\ContainerInterface;

/**
 * @internal
 */
final class EmptyDependencyContainer implements ContainerInterface
{
    public function get(string $id): void
    {
        throw new DependencyNotFoundException($id);
    }

    public function has(string $id): bool
    {
        return false;
    }
}
