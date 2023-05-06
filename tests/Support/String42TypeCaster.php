<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use ReflectionNamedType;
use ReflectionType;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\SkipTypeCastException;
use Yiisoft\Hydrator\TypeCasterInterface;

final class String42TypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed
    {
        if ($type instanceof ReflectionNamedType
            && $type->isBuiltin()
            && $type->getName() === 'string'
        ) {
            return '42';
        }

        throw new SkipTypeCastException();
    }
}
