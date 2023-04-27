<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

final class PreparePropertyModel
{
    public function __construct(
        private string $a,
    )
    {
        $this->a .= '!';
    }

    public function getA(): string
    {
        return $this->a;
    }
}
