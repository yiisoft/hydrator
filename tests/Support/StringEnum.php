<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support;

enum StringEnum: string
{
    case A = 'one';
    case B = 'two';
    case C = 'three';
}
