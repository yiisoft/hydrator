<?php

declare(strict_types=1);

use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use Yiisoft\CodeStyle\ConfigBuilder;

$finder = (new Finder())->in([
    __DIR__ . '/config',
    __DIR__ . '/src',
    __DIR__ . '/tests',
])->exclude([
    'Php82',
    'Php84',
]);

return ConfigBuilder::build()
    ->setCacheFile(__DIR__ . '/runtime/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@Yiisoft/Core' => true,
        '@Yiisoft/Core:risky' => true,
    ])
    ->setFinder($finder);
