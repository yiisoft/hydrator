<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe;

final class Car
{
    public function __construct(
        private string $name,
        private Engine $engine,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEngine(): Engine
    {
        return $this->engine;
    }
}
