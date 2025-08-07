<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php84\Hydrator\PublicPrivateSetProperty;

abstract class Figure
{
    public private(set) ?int $radius = null;
}
