<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute;

use Attribute;

/**
 * Attribute that mark a class property or constructor parameter for as non-used on hydration.
 */
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class SkipHydration
{
}
