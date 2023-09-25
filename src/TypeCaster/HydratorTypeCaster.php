<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use LogicException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\Result;

use Yiisoft\Hydrator\TypeCasterWithHydratorInterface;

use function is_array;

/**
 * Casts arrays to objects.
 */
final class HydratorTypeCaster implements TypeCasterWithHydratorInterface
{
    /**
     * @param HydratorInterface $hydrator Hydrator to use for casting arrays to objects.
     */
    private ?HydratorInterface $hydrator = null;

    public function setHydrator(HydratorInterface $hydrator): void
    {
        $this->hydrator = $hydrator;
    }

    public function cast(mixed $value, ?ReflectionType $type): Result
    {
        if ($this->hydrator === null) {
            throw new LogicException('Hydrator don\'t set.');
        }

        if (!is_array($value)) {
            return Result::fail();
        }

        if ($type instanceof ReflectionNamedType) {
            return $this->castInternal($value, $type);
        }

        if (!$type instanceof ReflectionUnionType) {
            return Result::fail();
        }

        foreach ($type->getTypes() as $t) {
            if (!$t instanceof ReflectionNamedType) {
                continue;
            }

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
            /**
             * @psalm-suppress PossiblyNullReference `$this->hydrator` check on null in `cast()` method.
             */
            return Result::success(
                $this->hydrator->create($class, $value)
            );
        }

        return Result::fail();
    }
}
