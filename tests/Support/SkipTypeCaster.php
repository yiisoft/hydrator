<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;
use Yiisoft\Hydrator\Result;

final class SkipTypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, TypeCastContext $context): Result
    {
        return Result::fail();
    }
}
