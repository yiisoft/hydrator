<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Internal;

use ReflectionClass;
use Yiisoft\Hydrator\DataInterface;

/**
 * @internal
 */
final class ObjectFactoryWithoutConstructor implements InternalObjectFactoryInterface
{
    public function create(ReflectionClass $reflectionClass, DataInterface $data): array
    {
        return [
            $reflectionClass->newInstanceWithoutConstructor(),
            [],
        ];
    }
}
