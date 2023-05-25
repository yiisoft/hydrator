<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionType;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Hydrator\Value;

final class NoTypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, ?ReflectionType $type): Value
    {
        return Value::success($value);
    }
}
