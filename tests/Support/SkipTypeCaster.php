<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use ReflectionType;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Hydrator\Result;

final class SkipTypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, ?ReflectionType $type): Result
    {
        return Result::fail();
    }
}
