<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

use Yiisoft\Hydrator\Attribute\NoPopulate;

final class NoPopulateModel
{
    #[NoPopulate]
    public ?int $a = null;
    public ?int $b = null;

    public function __construct(
        #[NoPopulate]
        public ?int $c = null,
        public ?int $d = null,
    ) {}
}
