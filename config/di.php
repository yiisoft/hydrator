<?php

declare(strict_types=1);

use Yiisoft\Definitions\Reference;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\SimpleHydrator;
use Yiisoft\Hydrator\TypeCaster\NoTypeCaster;

return [
    HydratorInterface::class => SimpleHydrator::class,
    SimpleHydrator::class => [
        '__construct()' => [
            'typeCaster' => Reference::to(NoTypeCaster::class),
        ],
    ],
];
