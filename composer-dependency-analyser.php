<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->disableComposerAutoloadPathScan()
    ->setFileExtensions(['php'])
    ->addPathToScan(__DIR__ . '/config', isDev: false)
    ->addPathToScan(__DIR__ . '/src', isDev: false)
    ->addPathToScan(__DIR__ . '/tests', isDev: true)
    ->ignoreErrorsOnExtension('ext-intl', [ErrorType::SHADOW_DEPENDENCY])
    // Multibyte trim functions come either from "mbstring" extension since PHP 8.4, or from
    // "symfony/polyfill-mbstring" package. The package is required for dev only, to run tests on PHP older than
    // 8.4, and is suggested to users, but intentionally not required in production.
    ->ignoreErrorsOnPackage('symfony/polyfill-mbstring', [ErrorType::DEV_DEPENDENCY_IN_PROD]);
