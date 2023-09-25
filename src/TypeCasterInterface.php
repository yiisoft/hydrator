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
     * @param TypeCastContext $context Type cast context.
     *
     * @return Result The result object.
     */
    public function cast(mixed $value, TypeCastContext $context): Result;
}
