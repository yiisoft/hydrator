<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Yiisoft\Definitions\Reference;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\HydratorInterface;

return [
    HydratorInterface::class => [
        'class' => Hydrator::class,
        '__construct()' => [
            'attributeResolverContainer' => Reference::to(ContainerInterface::class),
            'dependencyContainer' => Reference::to(ContainerInterface::class),
        ],
    ],
];
