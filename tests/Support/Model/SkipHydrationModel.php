<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

use Yiisoft\Hydrator\Attribute\SkipHydration;

final class SkipHydrationModel
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
