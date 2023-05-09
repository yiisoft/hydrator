<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\NestedModel;

final class Name
{
    public function __construct(
        private string $first = '',
        private string $last = '',
    ) {
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getLast(): string
    {
        return $this->last;
    }
}
