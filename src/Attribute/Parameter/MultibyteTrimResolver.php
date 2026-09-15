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
 * Resolver for {@see MultibyteTrim} attribute.
 */
final class MultibyteTrimResolver implements ParameterAttributeResolverInterface
{
    /**
     * @param string|null $characters The list of characters to strip when it is not specified in the attribute.
     *
     * @throws LogicException When `mb_trim()` function is not available.
     */
    public function __construct(
        private readonly ?string $characters = null,
    ) {
        if (!function_exists('mb_trim')) {
            throw new LogicException(
                'MultibyteTrim attribute requires "mb_trim()" function that is provided by "mbstring" extension since PHP 8.4 or by "symfony/polyfill-mbstring" package.',
            );
        }
    }

    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        ParameterAttributeResolveContext $context,
    ): Result {
        if (!$attribute instanceof MultibyteTrim) {
            throw new UnexpectedAttributeException(MultibyteTrim::class, $attribute);
        }

        if (!$context->isResolved()) {
            return Result::fail();
        }

        $resolvedValue = $context->getResolvedValue();
        if (!is_string($resolvedValue)) {
            return Result::fail();
        }

        return Result::success(
            mb_trim($resolvedValue, $attribute->characters ?? $this->characters),
        );
    }
}
