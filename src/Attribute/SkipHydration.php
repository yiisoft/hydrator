<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute;

use Attribute;

/**
 * Skip hydration of a property or parameter.
 */
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class SkipHydration
{
}
