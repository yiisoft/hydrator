<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Yiisoft\Hydrator\Tests\Support\IntegerEnum;
use Yiisoft\Hydrator\Tests\Support\StringEnum;

final class EnumsByDefault
{
    public function __construct(
        public string $string,
        public int $integer,
        public StringEnum $stringEnum,
        public IntegerEnum $integerEnum,
    ) {
    }
}
