<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Typecaster;

use ReflectionType;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypecasterInterface;

final class NoTypecaster implements TypecasterInterface
{
    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed
    {
        return $value;
    }
}
