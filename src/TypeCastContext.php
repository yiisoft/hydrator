<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;

final class TypeCastContext
{
    public function __construct(
        private HydratorInterface $hydrator,
        private ReflectionParameter|ReflectionProperty $item,
    ) {
    }

    public function getReflectionType(): ?ReflectionType
    {
        return $this->item->getType();
    }

    public function getHydrator(): HydratorInterface
    {
        return $this->hydrator;
    }
}
