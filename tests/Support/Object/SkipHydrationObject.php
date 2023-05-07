<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Object;

use Yiisoft\Hydrator\Attribute\SkipHydration;

final class SkipHydrationObject
{
    #[SkipHydration]
    public ?int $a = null;
    public ?int $b = null;

    public function __construct(
        #[SkipHydration]
        public ?int $c = null,
        public ?int $d = null,
    ) {
    }
}
