<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionType;

/**
 * Casts value to a type obtained from {@see ReflectionType} passed.
 */
interface TypeCasterInterface
{
    /**
     * @param mixed $value Value to cast.
     * @param ReflectionType|null $type Type to cast to.
     * @throws SkipTypeCastException If type casting should be skipped.
     */
    public function cast(mixed $value, ?ReflectionType $type): mixed;
}
