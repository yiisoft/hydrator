<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use ReflectionType;
use Yiisoft\Hydrator\SkipTypeCastException;
use Yiisoft\Hydrator\TypeCasterInterface;

final class SkipTypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, ?ReflectionType $type): mixed
    {
        throw new SkipTypeCastException();
    }
}
