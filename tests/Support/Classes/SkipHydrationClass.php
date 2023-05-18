<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Attribute\SkipHydration;

final class SkipHydrationClass
{
    #[SkipHydration]
    public ?int $a = null;
    public ?int $b = null;

    public function __construct(
        #[SkipHydration]
        public ?int $c = null,
        public ?int $d = null,
    ) {
        if ($this->d !== null) {
            $this->d += 100;
        }
    }

    public ?int $e = null;
}
