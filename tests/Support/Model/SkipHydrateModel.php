<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

use Yiisoft\Hydrator\Attribute\SkipHydrate;

final class SkipHydrateModel
{
    #[SkipHydrate]
    public ?int $a = null;
    public ?int $b = null;

    public function __construct(
        #[SkipHydrate]
        public ?int $c = null,
        public ?int $d = null,
    ) {}
}
