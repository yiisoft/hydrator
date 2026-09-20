<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Attribute;

/**
 * Strip whitespace (or other characters) from the beginning and end of a resolved string value.
 *
 * In multibyte mode, Unicode whitespace characters, such as `U+00A0` (no-break space), are stripped as well.
 * It requires the `mb_trim()` function provided by the `mbstring` PHP extension since PHP 8.4, or by the
 * `symfony/polyfill-mbstring` package on earlier versions.
 *
 * @see https://www.php.net/manual/function.trim.php
 * @see https://www.php.net/manual/function.mb-trim.php
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class Trim implements ParameterAttributeInterface
{
    /**
     * @param string|null $characters The list of all characters that you want to be stripped. With `..` you can
     * specify a range of characters, both in the default and in the multibyte mode. This syntax is deprecated and
     * will be removed in the next major version.
     * @param bool|null $multibyte Whether to use multibyte-aware trimming. `null` means using the resolver default.
     * @param string|null $encoding The encoding to use in multibyte mode. `null` means using the resolver default.
     */
    public function __construct(
        public readonly ?string $characters = null,
        public readonly ?bool $multibyte = null,
        public readonly ?string $encoding = null,
    ) {
        TrimCharacters::checkDeprecatedRanges($characters);
    }

    public function getResolver(): string
    {
        return TrimResolver::class;
    }
}
