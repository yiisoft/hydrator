<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

use Yiisoft\Hydrator\TypeCastContext;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Hydrator\Result;

final class SkipTypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, TypeCastContext $context): Result
    {
        return Result::fail();
    }
}
