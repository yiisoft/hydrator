<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Typecast;

use ReflectionType;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypecastInterface;

final class NoTypecast implements TypecastInterface
{
    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed
    {
        return $value;
    }
}
