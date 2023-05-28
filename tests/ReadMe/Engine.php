<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe;

final class Engine
{
    public function __construct(
        private string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
