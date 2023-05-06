<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use ReflectionNamedType;
use ReflectionType;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\SkipTypecastException;
use Yiisoft\Hydrator\TypecasterInterface;

final class String42Typecaster implements TypecasterInterface
{
    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed
    {
        if ($type instanceof ReflectionNamedType
            && $type->isBuiltin()
            && $type->getName() === 'string'
        ) {
            return '42';
        }

        throw new SkipTypecastException();
    }
}
