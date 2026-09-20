<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use LogicException;

use function count;
use function function_exists;
use function mb_chr;
use function mb_ord;
use function mb_str_split;
use function str_contains;
use function trigger_error;

use const E_USER_DEPRECATED;
use const E_USER_WARNING;

/**
 * @internal Helper for handling the `characters` parameter of `Trim`, `LeftTrim` and `RightTrim` attributes and
 * resolvers.
 */
final class TrimCharacters
{
    private static ?bool $multibyteFunctionsExist = null;

    public static function checkDeprecatedRanges(?string $characters): void
    {
        if ($characters === null) {
            return;
        }

        if (str_contains($characters, '..')) {
            trigger_error(
                'The ".." range syntax of the "characters" parameter is deprecated and will be removed in the next major version.',
                E_USER_DEPRECATED,
            );
        }
    }

    /**
     * Checks whether all `mb_*` functions used by multibyte mode are available.
     */
    public static function multibyteFunctionsExist(): bool
    {
        return self::$multibyteFunctionsExist ??= function_exists('mb_trim')
            && function_exists('mb_ltrim')
            && function_exists('mb_rtrim')
            && function_exists('mb_str_split')
            && function_exists('mb_ord')
            && function_exists('mb_chr');
    }

    /**
     * Checks that all `mb_*` functions used by multibyte mode are available, when {@see $multibyte} is `true`.
     */
    public static function checkMultibyteFunctionsExist(?bool $multibyte): void
    {
        if ($multibyte !== true || self::multibyteFunctionsExist()) {
            return;
        }

        // @codeCoverageIgnoreStart
        throw new LogicException(
            'The "multibyte" parameter requires "mbstring" extension since PHP 8.4 or "symfony/polyfill-mbstring"'
            . ' package on earlier versions.',
        );
        // @codeCoverageIgnoreEnd
    }

    /**
     * Expands `..` ranges in {@see $characters} into an explicit, range-free character list, mirroring the
     * range parsing of native {@see trim()} (see `php_charmask()` in `ext/standard/string.c`), including its
     * `E_WARNING` messages and literal fallback for malformed ranges.
     */
    public static function expandRanges(string $characters, ?string $encoding): string
    {
        /** @var list<string> $chars */
        $chars = mb_str_split($characters, 1, $encoding);
        $count = count($chars);
        $result = '';

        for ($i = 0; $i < $count; $i++) {
            $char = $chars[$i];

            if ($i + 3 < $count && $chars[$i + 1] === '.' && $chars[$i + 2] === '.') {
                $startOrd = mb_ord($char, $encoding);
                $endOrd = mb_ord($chars[$i + 3], $encoding);

                if ($startOrd !== false && $endOrd !== false && $endOrd >= $startOrd) {
                    for ($ord = $startOrd; $ord <= $endOrd; $ord++) {
                        $rangeChar = mb_chr($ord, $encoding);
                        if ($rangeChar !== false) {
                            $result .= $rangeChar;
                        }
                    }
                    $i += 3;
                    continue;
                }
            }

            if ($i + 1 < $count && $char === '.' && $chars[$i + 1] === '.') {
                if ($i === 0) {
                    trigger_error("Invalid '..'-range, no character to the left of '..'", E_USER_WARNING);
                    continue;
                }

                if ($i + 2 >= $count) {
                    trigger_error("Invalid '..'-range, no character to the right of '..'", E_USER_WARNING);
                    continue;
                }

                $leftOrd = mb_ord($chars[$i - 1], $encoding);
                $rightOrd = mb_ord($chars[$i + 2], $encoding);

                if ($leftOrd !== false && $rightOrd !== false && $leftOrd > $rightOrd) {
                    trigger_error("Invalid '..'-range, '..'-range needs to be incrementing", E_USER_WARNING);
                    continue;
                }

                trigger_error("Invalid '..'-range", E_USER_WARNING);
                continue;
            }

            $result .= $char;
        }

        return $result;
    }
}
