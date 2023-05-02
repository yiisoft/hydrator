<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class SkipHydrate
{
}
