<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Yiisoft\CodeStyle\Rector\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(php81: true)
    ->withSets([
        SetList::YII_CORE,
    ])
    ->withSkip([
        __DIR__ . '/tests/Support/Classes/SimpleClass.php',
        __DIR__ . '/tests/TestEnvironments/Php84',
    ]);
