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

    /**
     * Runs {@see $callback} with a temporary error handler installed, capturing every triggered error instead of
     * letting it propagate (which would otherwise fail the test suite via `failOnWarning`).
     *
     * @return list<array{int, string}> Captured `[$errno, $errstr]` pairs, in the order they were triggered.
     */
    public static function captureErrors(Closure $callback): array
    {
        $errors = [];

        set_error_handler(static function (int $errno, string $errstr) use (&$errors): bool {
            $errors[] = [$errno, $errstr];
            return true;
        });

        try {
            $callback();
        } finally {
            restore_error_handler();
        }

        return $errors;
    }

    public static function createTypeCastContext(Closure $closure): TypeCastContext
    {
        return new TypeCastContext(
            new Hydrator(),
            self::getFirstParameter($closure),
        );
    }
}
