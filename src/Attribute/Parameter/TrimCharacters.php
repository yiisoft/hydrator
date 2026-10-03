<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use LogicException;
use ValueError;

use function count;
use function function_exists;
use function mb_chr;
use function mb_convert_encoding;
use function mb_internal_encoding;
use function mb_ord;
use function mb_str_split;
use function mb_substr_count;
use function sprintf;
use function str_contains;
use function str_repeat;
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

    /**
     * Triggers a deprecation notice when {@see $characters} contain the `..` range syntax. In multibyte mode,
     * {@see $characters} are treated as a string in the {@see $encoding}.
     */
    public static function checkDeprecatedRanges(?string $characters, bool $multibyte, ?string $encoding): void
    {
        if ($characters === null) {
            return;
        }

        $hasRanges = $multibyte
            ? mb_substr_count($characters, str_repeat(self::dot($encoding), 2), $encoding) > 0
            : str_contains($characters, '..');

        if ($hasRanges) {
            trigger_error(
                'The ".." range syntax of the "characters" parameter is deprecated and will be removed in the next major version.',
                E_USER_DEPRECATED,
            );
        }
    }

    /**
     * Checks that all `mb_*` functions used by multibyte mode are available, when {@see $multibyte} is `true`.
     */
    public static function checkMultibyteFunctionsExist(?bool $multibyte): void
    {
        if ($multibyte !== true) {
            return;
        }

        self::$multibyteFunctionsExist ??= function_exists('mb_trim')
            && function_exists('mb_ltrim')
            && function_exists('mb_rtrim')
            && function_exists('mb_str_split')
            && function_exists('mb_substr_count')
            && function_exists('mb_ord')
            && function_exists('mb_chr')
            && function_exists('mb_convert_encoding')
            && function_exists('mb_internal_encoding');

        if (self::$multibyteFunctionsExist) {
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
        $encoding ??= mb_internal_encoding();

        /** @var list<string> $chars */
        $chars = mb_str_split($characters, 1, $encoding);
        $count = count($chars);
        $dot = self::dot($encoding);
        $native = self::supportsCodePointFunctions($encoding);
        $result = '';

        for ($i = 0; $i < $count; $i++) {
            $char = $chars[$i];

            if ($i + 3 < $count && $chars[$i + 1] === $dot && $chars[$i + 2] === $dot) {
                $startOrd = self::ord($char, $encoding, $native);
                $endOrd = self::ord($chars[$i + 3], $encoding, $native);

                if ($startOrd !== false && $endOrd !== false && $endOrd >= $startOrd) {
                    for ($ord = $startOrd; $ord <= $endOrd; $ord++) {
                        $rangeChar = self::chr($ord, $encoding, $native);
                        if ($rangeChar !== false) {
                            $result .= $rangeChar;
                        }
                    }
                    $i += 3;
                    continue;
                }
            }

            if ($i + 1 < $count && $char === $dot && $chars[$i + 1] === $dot) {
                if ($i === 0) {
                    trigger_error("Invalid '..'-range, no character to the left of '..'", E_USER_WARNING);
                    continue;
                }

                if ($i + 2 >= $count) {
                    trigger_error("Invalid '..'-range, no character to the right of '..'", E_USER_WARNING);
                    continue;
                }

                $leftOrd = self::ord($chars[$i - 1], $encoding, $native);
                $rightOrd = self::ord($chars[$i + 2], $encoding, $native);

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

    /**
     * Returns the `.` character in the {@see $encoding}. It is not a single `.` byte in encodings that are not
     * ASCII-compatible, such as `UTF-16LE` or `UTF-32`.
     */
    private static function dot(?string $encoding): string
    {
        $encoding ??= mb_internal_encoding();

        $dot = mb_convert_encoding('.', $encoding, 'UTF-8');
        if ($dot === false) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                sprintf('Failed to convert the "." character to the "%s" encoding.', $encoding),
            );
            // @codeCoverageIgnoreEnd
        }

        return $dot;
    }

    /**
     * Checks whether {@see mb_ord()} and {@see mb_chr()} support the {@see $encoding}. They don't support stateful
     * encodings, such as `UTF-7` or `ISO-2022-JP`.
     */
    private static function supportsCodePointFunctions(string $encoding): bool
    {
        try {
            mb_chr(0x2E, $encoding);
        } catch (ValueError) {
            return false;
        }

        return true;
    }

    /**
     * Returns the Unicode code point of the {@see $char} in the {@see $encoding}, or `false` if the character is
     * invalid. When {@see $native} is `false`, the character is converted to `UTF-8` first. In this case, it is always
     * valid, since {@see mb_str_split()} replaces invalid characters with a substitute character.
     */
    private static function ord(string $char, string $encoding, bool $native): int|false
    {
        if ($native) {
            return mb_ord($char, $encoding);
        }

        return mb_ord((string) mb_convert_encoding($char, 'UTF-8', $encoding), 'UTF-8');
    }

    /**
     * Returns the character with the Unicode {@see $codePoint} in the {@see $encoding}, or `false` if it is not
     * representable in the encoding. When {@see $native} is `false`, the character is converted from `UTF-8`.
     */
    private static function chr(int $codePoint, string $encoding, bool $native): string|false
    {
        if ($native) {
            return mb_chr($codePoint, $encoding);
        }

        $utf8Char = mb_chr($codePoint, 'UTF-8');
        if ($utf8Char === false) {
            return false;
        }

        $char = (string) mb_convert_encoding($utf8Char, $encoding, 'UTF-8');
        return mb_convert_encoding($char, 'UTF-8', $encoding) === $utf8Char ? $char : false;
    }
}
