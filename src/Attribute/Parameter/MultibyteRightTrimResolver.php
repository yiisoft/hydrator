<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use LogicException;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Result;

use function function_exists;
use function is_string;

/**
 * Resolver for {@see MultibyteRightTrim} attribute.
 */
final class MultibyteRightTrimResolver implements ParameterAttributeResolverInterface
{
    /**
     * @param string|null $characters The list of characters to strip when it is not specified in the attribute.
     *
     * @throws LogicException When `mb_rtrim()` function is not available.
     */
    public function __construct(
        private readonly ?string $characters = null,
    ) {
        if (!function_exists('mb_rtrim')) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                'MultibyteRightTrim attribute requires "mb_rtrim()" function that is provided by "mbstring" extension since PHP 8.4 or by "symfony/polyfill-mbstring" package.',
            );
            // @codeCoverageIgnoreEnd
        }
    }

    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        ParameterAttributeResolveContext $context,
    ): Result {
        if (!$attribute instanceof MultibyteRightTrim) {
            throw new UnexpectedAttributeException(MultibyteRightTrim::class, $attribute);
        }

        if (!$context->isResolved()) {
            return Result::fail();
        }

        $resolvedValue = $context->getResolvedValue();
        if (!is_string($resolvedValue)) {
            return Result::fail();
        }

        return Result::success(
            mb_rtrim($resolvedValue, $attribute->characters ?? $this->characters),
        );
    }
}
