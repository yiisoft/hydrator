<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Object\ObjectPropertyModel;

final class RedCar extends Car
{
    public function getColor(): string
    {
        return 'red';
    }
}
