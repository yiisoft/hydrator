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

        $isNull = is_null($value);
        $isInt = is_int($value);
        $isFloat = is_float($value);
        $isString = is_string($value);
        $isBool = is_bool($value);
        $isArray = is_array($value);
        $isObject = is_object($value);
        $isStringable = $value instanceof Stringable;

        foreach ($types as $t) {
            if ($isNull && $t->allowsNull()) {
                return null;
            }
            if ($t->isBuiltin()) {
                switch ($t->getName()) {
                    case 'string':
                        if ($isString) {
                            return (string) $value;
                        }
                        break;

                    case 'int':
                        if ($isInt) {
                            return $value;
                        }
                        break;

                    case 'float':
                        if ($isFloat) {
                            return $value;
                        }
                        break;

                    case 'bool':
                        if ($isBool) {
                            return (bool) $value;
                        }
                        break;

                    case 'array':
                        if ($isArray) {
                            return $value;
                        }
                        break;
                }
            }
        }

        foreach ($types as $t) {
            if ($t->isBuiltin()) {
                switch ($t->getName()) {
                    case 'string':
                        if ($isInt || $isFloat || $isBool || $isNull || $isStringable) {
                            return (string) $value;
                        }
                        break;

                    case 'int':
                        if ($isBool || $isFloat || $isNull) {
                            return (int) $value;
                        }
                        if ($isStringable || $isString) {
                            return (int) NumericHelper::normalize((string) $value);
                        }
                        break;

                    case 'float':
                        if ($isInt || $isBool || $isNull) {
                            return (float) $value;
                        }
                        if ($isStringable || $isString) {
                            return (float) NumericHelper::normalize((string) $value);
                        }
                        break;

                    case 'bool':
                        if ($isInt || $isFloat || $isString || $isNull || $isArray || $isObject) {
                            return (bool) $value;
                        }
                        break;
                }
                continue;
            }

            $class = $t->getName();
            if ($isObject) {
                if (is_a($value, $class)) {
                    return $value;
                }
            } elseif ($isArray) {
                $reflection = new ReflectionClass($class);
                if ($reflection->isInstantiable()) {
                    return $hydrator->create($class, $value);
                }
            }
        }

        throw new SkipTypecastException();
    }
}
