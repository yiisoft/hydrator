<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use LogicException;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;

final class TypeCastContext
{
    private ReflectionParameter|ReflectionProperty|null $item = null;

    public function __construct(
        private HydratorInterface $hydrator,
    ) {
    }

    public function withItem(ReflectionParameter|ReflectionProperty $item): self
    {
        $new = clone $this;
        $new->item = $item;
        return $new;
    }

    public function getReflectionType(): ?ReflectionType
    {
        if ($this->item === null) {
            throw new LogicException('Type cast context is don\'t contain reflection property or parameter.');
        }

        return $this->item->getType();
    }

    public function getHydrator(): HydratorInterface
    {
        return $this->hydrator;
    }
}
