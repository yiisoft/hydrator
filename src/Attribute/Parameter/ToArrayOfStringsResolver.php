<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use LogicException;
use Stringable;
use Traversable;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Result;

use function function_exists;
use function is_scalar;

final class ToArrayOfStringsResolver implements ParameterAttributeResolverInterface
{
    /**
     * @param bool $multibyte Whether to use multibyte-aware trimming that strips Unicode whitespace characters
     * such as `U+00A0` (no-break space) as well, when it is not specified in the attribute. Requires PHP 8.4 or
     * later with `mbstring` extension, or `symfony/polyfill-mbstring` package.
     * @param string|null $encoding The encoding to use in multibyte mode when it is not specified in the attribute.
     */
    public function __construct(
        private readonly bool $multibyte = false,
        private readonly ?string $encoding = null,
    ) {}

    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        ParameterAttributeResolveContext $context,
    ): Result {
        if (!$attribute instanceof ToArrayOfStrings) {
            throw new UnexpectedAttributeException(ToArrayOfStrings::class, $attribute);
        }

        if (!$context->isResolved()) {
            return Result::fail();
        }

        $resolvedValue = $context->getResolvedValue();
        if (is_iterable($resolvedValue)) {
            $array = array_map(
                $this->castValueToString(...),
                $resolvedValue instanceof Traversable ? iterator_to_array($resolvedValue) : $resolvedValue,
            );
        } else {
            $value = $this->castValueToString($resolvedValue);
            /**
             * @var string[] $array We assume valid regular expression is used here, so `preg_split()` always returns
             * an array of strings.
             */
            $array = $attribute->splitResolvedValue
                ? preg_split('~' . $attribute->separator . '~u', $value)
                : [$value];
        }

        if ($attribute->trim) {
            $multibyte = $attribute->multibyte ?? $this->multibyte;
            $encoding = $attribute->encoding ?? $this->encoding;

            if ($multibyte && !function_exists('mb_trim')) {
                // @codeCoverageIgnoreStart
                throw new LogicException(
                    'The "multibyte" parameter requires "mb_trim()" function that is provided by "mbstring" extension since PHP 8.4 or by "symfony/polyfill-mbstring" package.',
                );
                // @codeCoverageIgnoreEnd
            }

            $array = array_map(
                $multibyte ? static fn(string $value): string => mb_trim($value, null, $encoding) : trim(...),
                $array,
            );
        }

        if ($attribute->removeEmpty) {
            $array = array_filter(
                $array,
                static fn(string $value): bool => $value !== '',
            );
        }

        return Result::success($array);
    }

    private function castValueToString(mixed $value): string
    {
        return is_scalar($value) || $value instanceof Stringable ? (string) $value : '';
    }
}
