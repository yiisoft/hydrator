<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionType;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Hydrator\Result;

/**
 * Doesn't cast value at all leaving it as is.
 */
final class NoTypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, ?ReflectionType $type): Result
    {
        return Result::success($value);
    }
}
