<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Stringable;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Hydrator\Value;
use Yiisoft\Strings\NumericHelper;

use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;

final class SimpleTypeCaster implements TypeCasterInterface
{
    private ?HydratorInterface $hydrator = null;

    public function withHydrator(HydratorInterface $hydrator): self
    {
        $new = clone $this;
        $new->hydrator = $hydrator;
        return $new;
    }

    public function cast(mixed $value, ?ReflectionType $type): Value
    {
        if ($type instanceof ReflectionNamedType) {
            $types = [$type];
        } elseif ($type instanceof ReflectionUnionType) {
            $types = array_filter(
                $type->getTypes(),
                static fn(mixed $type) => $type instanceof ReflectionNamedType,
            );
        } elseif ($type === null) {
            return Value::success($value);
        } else {
            return Value::fail();
        }

        foreach ($types as $t) {
            if (null === $value && $t->allowsNull()) {
                return Value::success(null);
            }
            if ($t->isBuiltin()) {
                switch ($t->getName()) {
                    case 'string':
                        if (is_string($value)) {
                            return Value::success($value);
                        }
                        break;

                    case 'int':
                        if (is_int($value)) {
                            return Value::success($value);
                        }
                        break;

                    case 'float':
                        if (is_float($value)) {
                            return Value::success($value);
                        }
                        break;

                    case 'bool':
                        if (is_bool($value)) {
                            return Value::success($value);
                        }
                        break;

                    case 'array':
                        if (is_array($value)) {
                            return Value::success($value);
                        }
                        break;
                }
            }
        }

        foreach ($types as $t) {
            if ($t->isBuiltin()) {
                switch ($t->getName()) {
                    case 'string':
                        if (is_scalar($value) || null === $value || $value instanceof Stringable) {
                            return Value::success((string) $value);
                        }
                        break;

                    case 'int':
                        if (is_bool($value) || is_float($value) || null === $value) {
                            return Value::success((int) $value);
                        }
                        if ($value instanceof Stringable || is_string($value)) {
                            return Value::success((int) NumericHelper::normalize((string) $value));
                        }
                        break;

                    case 'float':
                        if (is_int($value) || is_bool($value) || null === $value) {
                            return Value::success((float) $value);
                        }
                        if ($value instanceof Stringable || is_string($value)) {
                            return Value::success((float) NumericHelper::normalize((string) $value));
                        }
                        break;

                    case 'bool':
                        if (is_scalar($value) || null === $value || is_array($value) || is_object($value)) {
                            return Value::success((bool) $value);
                        }
                        break;
                }
                continue;
            }

            $class = $t->getName();
            if (is_object($value)) {
                if (is_a($value, $class)) {
                    return Value::success($value);
                }
            } elseif (is_array($value) && $this->hydrator !== null) {
                $reflection = new ReflectionClass($class);
                if ($reflection->isInstantiable()) {
                    return Value::success(
                        $this->hydrator->create($class, $value)
                    );
                }
            }
        }

        return Value::fail();
    }
}
