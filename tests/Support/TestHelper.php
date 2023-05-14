<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use Closure;
use ReflectionFunction;
use ReflectionParameter;

final class TestHelper
{
    public static function getFirstParameter(Closure $closure): ReflectionParameter
    {
        $parameters = (new ReflectionFunction($closure))->getParameters();

        return reset($parameters);
    }
}
