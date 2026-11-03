<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use Yiisoft\Hydrator\Tests\Support\Attribute\InvalidDataResolver;

/**
 * Enum with a data attribute whose resolver throws an exception when it's applied.
 */
#[InvalidDataResolver]
enum InvalidDataResolverEnum: int
{
    case A = 1;
}
