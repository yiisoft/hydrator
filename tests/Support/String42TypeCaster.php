<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use ReflectionNamedType;
use ReflectionType;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Hydrator\Result;

final class String42TypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, ?ReflectionType $type): Result
    {
        if ($type instanceof ReflectionNamedType
            && $type->isBuiltin()
            && $type->getName() === 'string'
        ) {
            return Result::success('42');
        }

        return Result::fail();
    }
}
