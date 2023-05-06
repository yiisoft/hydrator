<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model;

use Stringable;
use Yiisoft\Hydrator\Attribute\Parameter\Di;
use Yiisoft\Hydrator\Attribute\Parameter\ToString;

final class ConstructorParameterAttributesModel
{
    public function __construct(
        #[ToString]
        private string $a,
        #[Di('stringable42')]
        private Stringable $stringable,
    ) {}

    public function getA(): string
    {
        return $this->a;
    }

    public function getString(): string
    {
        return (string) $this->stringable;
    }
}
