<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Result;

use function is_string;

/**
 * Resolver for {@see RightTrim} attribute.
 */
final class RightTrimResolver implements ParameterAttributeResolverInterface
{
    /**
     * @param string|null $characters The list of characters to strip when it is not specified in the attribute.
     * With `..` you can specify a range of characters, both in the default and in the multibyte mode. This syntax
     * is deprecated and will be removed in the next major version.
     * @param bool $multibyte Whether to use multibyte-aware trimming when it is not specified in the attribute.
     * @param string|null $encoding The encoding to use in multibyte mode when it is not specified in the attribute.
     */
    public function __construct(
        private readonly ?string $characters = null,
        private readonly bool $multibyte = false,
        private readonly ?string $encoding = null,
    ) {
        TrimCharacters::checkDeprecatedRanges($characters);
        TrimCharacters::checkMultibyteFunctionsExist($multibyte);
    }

    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        ParameterAttributeResolveContext $context,
    ): Result {
        if (!$attribute instanceof RightTrim) {
            throw new UnexpectedAttributeException(RightTrim::class, $attribute);
        }

        if (!$context->isResolved()) {
            return Result::fail();
        }

        $resolvedValue = $context->getResolvedValue();
        if (!is_string($resolvedValue)) {
            return Result::fail();
        }

        $characters = $attribute->characters ?? $this->characters;
        $multibyte = $attribute->multibyte ?? $this->multibyte;
        $encoding = $attribute->encoding ?? $this->encoding;

        if (!$multibyte) {
            return Result::success(
                $characters === null ? rtrim($resolvedValue) : rtrim($resolvedValue, $characters),
            );
        }

        return Result::success(
            mb_rtrim(
                $resolvedValue,
                $characters === null ? null : TrimCharacters::expandRanges($characters, $encoding),
                $encoding,
            ),
        );
    }
}
