<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model\ObjectPropertyModel;

class Car
{
    public function getColor(): string
    {
        return 'black';
    }
}
