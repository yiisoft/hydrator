<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Attribute;

/**
 * Multibyte-aware version of {@see LeftTrim}. It strips whitespace (or other characters) from the beginning of
 * a resolved string value, taking Unicode whitespace characters, such as `U+00A0` (no-break space), into account.
 *
 * Requires PHP 8.4 or later with `mbstring` extension, or `symfony/polyfill-mbstring` package.
 *
 * @see https://www.php.net/manual/function.mb-ltrim.php
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class MultibyteLeftTrim implements ParameterAttributeInterface
{
    /**
     * @param string|null $characters The list of all characters that you want to be stripped. Unlike {@see LeftTrim},
     * the `..` range syntax is not supported, every character is treated literally.
     */
    public function __construct(
        public readonly ?string $characters = null,
    ) {}

    public function getResolver(): string
    {
        return MultibyteLeftTrimResolver::class;
    }
}
