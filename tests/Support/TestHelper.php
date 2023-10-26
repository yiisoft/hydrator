<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use Closure;
use ReflectionFunction;
use ReflectionParameter;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;

final class TestHelper
{
    public static function getFirstParameter(Closure $closure): ReflectionParameter
    {
        $parameters = (new ReflectionFunction($closure))->getParameters();

        return reset($parameters);
    }

    public static function createTypeCastContext(Closure $closure): TypeCastContext
    {
        return new TypeCastContext(
            new Hydrator(),
            self::getFirstParameter($closure),
        );
    }
}
