<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;

use function is_array;

/**
 * Casts arrays to objects.
 */
final class HydratorTypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, TypeCastContext $context): Result
    {
        $type = $context->getReflectionType();
        $hydrator = $context->getHydrator();

        if (!is_array($value)) {
            return Result::fail();
        }

        if ($type instanceof ReflectionNamedType) {
            return $this->castInternal($value, $type, $hydrator);
        }

        if (!$type instanceof ReflectionUnionType) {
            return Result::fail();
        }

        foreach ($type->getTypes() as $t) {
            if (!$t instanceof ReflectionNamedType) {
                continue;
            }

            $result = $this->castInternal($value, $t, $hydrator);
            if ($result->isResolved()) {
                return $result;
            }
        }

        return Result::fail();
    }

    private function castInternal(array $value, ReflectionNamedType $type, HydratorInterface $hydrator): Result
    {
        if ($type->isBuiltin()) {
            return Result::fail();
        }

        $class = $type->getName();

        $reflection = new ReflectionClass($class);
        if ($reflection->isInstantiable()) {
            return Result::success(
                $hydrator->create($class, $value)
            );
        }

        return Result::fail();
    }
}
