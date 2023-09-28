<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

interface DataInterface
{
    public function getValue(string $name): Result;
}
