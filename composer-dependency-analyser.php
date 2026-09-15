<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = (new Configuration())
    ->disableComposerAutoloadPathScan()
    ->setFileExtensions(['php'])
    ->addPathToScan(__DIR__ . '/config', isDev: false)
    ->addPathToScan(__DIR__ . '/src', isDev: false)
    ->addPathToScan(__DIR__ . '/tests', isDev: true)
    ->ignoreErrorsOnExtension('ext-intl', [ErrorType::SHADOW_DEPENDENCY]);

// Multibyte trim functions come either from "mbstring" extension since PHP 8.4, or from "symfony/polyfill-mbstring"
// package. The extension is only suggested to users, and the package is required for dev only, to run tests on PHP
// older than 8.4, so neither of them is intentionally required in production.
if (PHP_VERSION_ID < 80400) {
    $config->ignoreErrorsOnPackage('symfony/polyfill-mbstring', [ErrorType::DEV_DEPENDENCY_IN_PROD]);
} else {
    $config->ignoreErrorsOnExtension('ext-mbstring', [ErrorType::SHADOW_DEPENDENCY]);
}

return $config;
