<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

interface TypeCasterWithHydratorInterface extends TypeCasterInterface
{
    public function setHydrator(HydratorInterface $hydrator): void;
}
