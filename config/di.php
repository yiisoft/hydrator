<?php

declare(strict_types=1);

use Yiisoft\Definitions\Reference;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\SimpleHydrator;
use Yiisoft\Hydrator\TypeCaster\NoTypeCaster;

return [
    HydratorInterface::class => Hydrator::class,
    Hydrator::class => [
        '__construct()' => [
            'decoratedHydrator' => Reference::to(SimpleHydrator::class),
            'typeCaster' => Reference::to(NoTypeCaster::class),
        ]
    ],
];
