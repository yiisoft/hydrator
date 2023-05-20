<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Stringable;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\SkipTypeCastException;
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
 * Hydrator is used on arrays to cast these to objects.
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

    public function cast(mixed $value, ?ReflectionType $type): mixed
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
            throw new SkipTypeCastException();
        }

        foreach ($types as $t) {
            if (null === $value && $t->allowsNull()) {
                return null;
            }
            if ($t->isBuiltin()) {
                switch ($t->getName()) {
                    case 'string':
                        if (is_string($value)) {
                            return $value;
                        }
                        break;

                    case 'int':
                        if (is_int($value)) {
                            return $value;
                        }
                        break;

                    case 'float':
                        if (is_float($value)) {
                            return $value;
                        }
                        break;

                    case 'bool':
                        if (is_bool($value)) {
                            return $value;
                        }
                        break;

                    case 'array':
                        if (is_array($value)) {
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
                        if (is_scalar($value) || null === $value || $value instanceof Stringable) {
                            return (string) $value;
                        }
                        break;

                    case 'int':
                        if (is_bool($value) || is_float($value) || null === $value) {
                            return (int) $value;
                        }
                        if ($value instanceof Stringable || is_string($value)) {
                            return (int) NumericHelper::normalize((string) $value);
                        }
                        break;

                    case 'float':
                        if (is_int($value) || is_bool($value) || null === $value) {
                            return (float) $value;
                        }
                        if ($value instanceof Stringable || is_string($value)) {
                            return (float) NumericHelper::normalize((string) $value);
                        }
                        break;

                    case 'bool':
                        if (is_scalar($value) || null === $value || is_array($value) || is_object($value)) {
                            return (bool) $value;
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
            } elseif (is_array($value) && $this->hydrator !== null) {
                $reflection = new ReflectionClass($class);
                if ($reflection->isInstantiable()) {
                    return $this->hydrator->create($class, $value);
                }
            }
        }

        throw new SkipTypeCastException();
    }
}
