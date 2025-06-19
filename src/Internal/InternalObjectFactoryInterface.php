<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Internal;

use ReflectionClass;
use Yiisoft\Hydrator\DataInterface;
use Yiisoft\Hydrator\Exception\NonInstantiableException;

/**
 * @internal
 */
interface InternalObjectFactoryInterface
{
    /**
     * @throws NonInstantiableException
     *
     * @psalm-template T of object
     * @psalm-param ReflectionClass<T> $reflectionClass
     * @psalm-return list{T, list<string>}
     */
    public function create(ReflectionClass $reflectionClass, DataInterface $data): array;
}
