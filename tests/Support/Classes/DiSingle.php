<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Attribute\Parameter\Di;

final class DiSingle
{
    #[Di]
    public EngineInterface $engine;
}
