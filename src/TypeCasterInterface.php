<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionType;

interface TypeCasterInterface
{
    public function cast(mixed $value, ?ReflectionType $type): Value;
}
