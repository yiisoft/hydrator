<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Attribute\Parameter\Di;

final class DiUnion
{
    #[Di]
    public EngineInterface|string $engine1;

    #[Di]
    public string|EngineInterface $engine2;

}
