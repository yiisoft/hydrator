<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Typecast;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Stringable;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\SkipTypecastException;
use Yiisoft\Hydrator\TypecastInterface;
use Yiisoft\Strings\NumericHelper;

use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_null;
use function is_object;
use function is_string;

final class SimpleTypecast implements TypecastInterface
{
    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed
    {
        if ($type instanceof ReflectionNamedType) {
            $types = [$type];
        } elseif ($type instanceof ReflectionUnionType) {
            $types = array_filter(
                $type->getTypes(),
                static fn(mixed $type) => $type instanceof ReflectionNamedType,
            );
        } elseif ($type === null) {
            return $value;
        } else {
            throw new SkipTypecastException();
        }

        foreach ($types as $t) {
            /** @psalm-trace $t */
            if ($t->isBuiltin()) {
                if ($t->allowsNull() && is_null($value)) {
                    return null;
                }
                switch ($t->getName()) {
                    case 'string':
                        if (
                            is_int($value)
                            || is_bool($value)
                            || is_float($value)
                            || is_string($value)
                            || is_null($value)
                            || $value instanceof Stringable
                        ) {
                            return (string) $value;
                        }
                        break;

                    case 'int':
                        if (
                            is_int($value)
                            || is_bool($value)
                            || is_float($value)
                            || is_null($value)
                        ) {
                            return (int) $value;
                        }
                        if ($value instanceof Stringable || is_string($value)) {
                            return (int) NumericHelper::normalize((string) $value);
                        }
                        break;

                    case 'float':
                        if (
                            is_int($value)
                            || is_bool($value)
                            || is_float($value)
                            || is_null($value)
                        ) {
                            return (float) $value;
                        }
                        if ($value instanceof Stringable || is_string($value)) {
                            return (float) NumericHelper::normalize((string) $value);
                        }
                        break;

                    case 'bool':
                        if (
                            is_int($value)
                            || is_bool($value)
                            || is_float($value)
                            || is_string($value)
                            || is_null($value)
                            || is_array($value)
                            || is_object($value)
                        ) {
                            return (bool) $value;
                        }
                        break;

                    case 'array':
                        if (is_array($value)) {
                            return $value;
                        }
                        break;
                }
                continue;
            }

            $class = $t->getName();
            if (is_object($value)) {
                if (is_a($value, $class)) {
                    return $value;
                }
            } elseif (is_array($value)) {
                $reflection = new ReflectionClass($class);
                if ($reflection->isInstantiable()) {
                    return $hydrator->create($class, $value);
                }
            }
        }

        throw new SkipTypecastException();
    }
}
