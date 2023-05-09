<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\ObjectPropertyModel;

final class RedCar extends Car
{
    public function getColor(): string
    {
        return 'red';
    }
}
