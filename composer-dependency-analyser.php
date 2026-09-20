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

// Multibyte trim support (`Trim`, `LeftTrim`, `RightTrim`, `ToArrayOfStrings` attributes and `TrimCharacters`
// helper) uses `mb_str_split()`, `mb_ord()`, `mb_chr()` and, since PHP 8.4, `mb_trim()`/`mb_ltrim()`/`mb_rtrim()`.
// All of them come either from the "mbstring" extension, or, for the trim functions on PHP older than 8.4, from
// the "symfony/polyfill-mbstring" package. The extension is only suggested to users, and the package is required
// for dev only, to run tests on PHP older than 8.4, so neither of them is intentionally required in production.
$config->ignoreErrorsOnExtension('ext-mbstring', [ErrorType::SHADOW_DEPENDENCY]);
if (PHP_VERSION_ID < 80400) {
    $config->ignoreErrorsOnPackage('symfony/polyfill-mbstring', [ErrorType::DEV_DEPENDENCY_IN_PROD]);
}

return $config;
