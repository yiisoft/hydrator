<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Stringable;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Strings\NumericHelper;

use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;

/**
 * Casts value to a type obtained from {@see ReflectionType} passed.
 * {@see Hydrator} is used on arrays to cast these to objects.
 */
final class SimpleTypeCaster implements TypeCasterInterface
{
    /**
     * @var HydratorInterface|null Hydrator to use to cast arrays to objects.
     */
    private ?HydratorInterface $hydrator = null;

    /**
     * @param HydratorInterface $hydrator Hydrator to use to cast arrays to objects.
     */
    public function withHydrator(HydratorInterface $hydrator): self
    {
        $new = clone $this;
        $new->hydrator = $hydrator;
        return $new;
    }

    public function cast(mixed $value, ?ReflectionType $type): Result
    {
        if ($type === null) {
            return Result::success($value);
        }

        if (!$type instanceof ReflectionNamedType && !$type instanceof ReflectionUnionType) {
            return Result::fail();
        }

        if ($type instanceof ReflectionNamedType) {
            $types = [$type];
        } else {
            $types = array_filter(
                $type->getTypes(),
                static fn(mixed $type) => $type instanceof ReflectionNamedType,
            );
        }

        /**
         * Find the best type name and value type match.
         * Example:
         * - when pass `42` to `int|string` type, `int` will be used;
         * - when pass `"42"` to `int|string` type, `string` will be used.
         */
        foreach ($types as $t) {
            if (null === $value && $t->allowsNull()) {
                return Result::success(null);
            }
            if (!$t->isBuiltin()) {
                continue;
            }
            switch ($t->getName()) {
                case 'string':
                    if (is_string($value)) {
                        return Result::success($value);
                    }
                    break;

                case 'int':
                    if (is_int($value)) {
                        return Result::success($value);
                    }
                    break;

                case 'float':
                    if (is_float($value)) {
                        return Result::success($value);
                    }
                    break;

                case 'bool':
                    if (is_bool($value)) {
                        return Result::success($value);
                    }
                    break;

                case 'array':
                    if (is_array($value)) {
                        return Result::success($value);
                    }
                    break;
            }
        }

        foreach ($types as $t) {
            if (!$t->isBuiltin()) {
                $class = $t->getName();
                if (is_object($value)) {
                    if ($value instanceof $class) {
                        return Result::success($value);
                    }
                    continue;
                }
                if (is_array($value) && $this->hydrator !== null) {
                    $reflection = new ReflectionClass($class);
                    if ($reflection->isInstantiable()) {
                        return Result::success(
                            $this->hydrator->create($class, $value)
                        );
                    }
                }
                continue;
            }
            switch ($t->getName()) {
                case 'string':
                    if (is_scalar($value) || null === $value || $value instanceof Stringable) {
                        return Result::success((string) $value);
                    }
                    break;

                case 'int':
                    if (is_bool($value) || is_float($value) || null === $value) {
                        return Result::success((int) $value);
                    }
                    if ($value instanceof Stringable || is_string($value)) {
                        return Result::success((int) NumericHelper::normalize((string) $value));
                    }
                    break;

                case 'float':
                    if (is_int($value) || is_bool($value) || null === $value) {
                        return Result::success((float) $value);
                    }
                    if ($value instanceof Stringable || is_string($value)) {
                        return Result::success((float) NumericHelper::normalize((string) $value));
                    }
                    break;

                case 'bool':
                    if (is_scalar($value) || null === $value || is_array($value) || is_object($value)) {
                        return Result::success((bool) $value);
                    }
                    break;
            }
        }

        return Result::fail();
    }
}
