<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCasterInterface;

use function is_array;

/**
 * Casts arrays to objects.
 */
final class HydratorTypeCaster implements TypeCasterInterface
{
    /**
     * @param HydratorInterface $hydrator Hydrator to use for casting arrays to objects.
     */
    public function __construct(
        private HydratorInterface $hydrator,
    ) {
    }

    public function cast(mixed $value, ?ReflectionType $type): Result
    {
        if (!is_array($value)) {
            return Result::fail();
        }

        if ($type instanceof ReflectionNamedType) {
            return $this->castInternal($value, $type);
        }

        if (!$type instanceof ReflectionUnionType) {
            return Result::fail();
        }

        $types = array_filter(
            $type->getTypes(),
            static fn(mixed $type) => $type instanceof ReflectionNamedType,
        );

        foreach ($types as $t) {
            $result = $this->castInternal($value, $t);
            if ($result->isResolved()) {
                return $result;
            }
        }

        return Result::fail();
    }

    private function castInternal(array $value, ReflectionNamedType $type): Result
    {
        if ($type->isBuiltin()) {
            return Result::fail();
        }

        $class = $type->getName();

        $reflection = new ReflectionClass($class);
        if ($reflection->isInstantiable()) {
            return Result::success(
                $this->hydrator->create($class, $value)
            );
        }

        return Result::fail();
    }
}
