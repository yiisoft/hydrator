<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionType;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCasterWithHydratorInterface;

/**
 * Allows using many type casters one by one before the value cast successfully.
 */
final class CompositeTypeCaster implements TypeCasterWithHydratorInterface
{
    /**
     * @var TypeCasterInterface[] Type casters to use.
     */
    private array $typeCasters;

    /**
     * @param TypeCasterInterface ...$typeCasters Type casters to use.
     */
    public function __construct(
        TypeCasterInterface ...$typeCasters
    ) {
        $this->typeCasters = $typeCasters;
    }

    public function setHydrator(HydratorInterface $hydrator): void
    {
        foreach ($this->typeCasters as $typeCaster) {
            if ($typeCaster instanceof TypeCasterWithHydratorInterface) {
                $typeCaster->setHydrator($hydrator);
            }
        }
    }

    public function cast(mixed $value, ?ReflectionType $type): Result
    {
        foreach ($this->typeCasters as $typeCaster) {
            $result = $typeCaster->cast($value, $type);
            if ($result->isResolved()) {
                return $result;
            }
        }

        return Result::fail();
    }
}
