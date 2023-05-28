<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe;

use Yiisoft\Hydrator\Attribute\Parameter\ToString;

final class Money
{
    public function __construct(
        #[ToString]
        private string $value,
        private string $currency,
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
