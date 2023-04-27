<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Attribute;
use Stringable;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\NotResolvedException;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;

use function is_bool;
use function is_float;
use function is_int;
use function is_null;
use function is_string;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class CastToString implements ParameterAttributeInterface, ParameterAttributeResolverInterface
{
    public function getResolver(): self
    {
        return $this;
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed
    {
        if (!$attribute instanceof self) {
            throw new UnexpectedAttributeException(self::class, $attribute);
        }

        if ($context->isResolved()) {
            $resolvedValue = $context->getResolvedValue();
            if (
                is_int($resolvedValue)
                || is_bool($resolvedValue)
                || is_float($resolvedValue)
                || is_string($resolvedValue)
                || is_null($resolvedValue)
                || $resolvedValue instanceof Stringable
            ) {
                return (string) $resolvedValue;
            }
            return '';
        }

        throw new NotResolvedException();
    }
}
