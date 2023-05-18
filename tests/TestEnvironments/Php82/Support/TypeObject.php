<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php82\Support;

use Stringable;

final class TypeObject
{
    public string|(CarInterface&Stringable) $unionIntersection = '.';
}
