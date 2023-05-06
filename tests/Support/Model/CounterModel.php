<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;

final class CounterModel
{
    #[Counter('a')]
    private string $a;

    public function __construct(
        string $a = '.',
        #[Counter('b')]
        private string $b = '.',
        #[Counter('c')]
        string $c = '.',
    ) {
    }
}
