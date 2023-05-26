<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Attribute;
use Stringable;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Value;
use Yiisoft\Hydrator\UnexpectedAttributeException;

/**
 * Converts the data value mapped to string before assigning it to a property or a parameter.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class ToString implements ParameterAttributeInterface, ParameterAttributeResolverInterface
{
    public function getResolver(): self
    {
        return $this;
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): Value
    {
        if (!$attribute instanceof self) {
            throw new UnexpectedAttributeException(self::class, $attribute);
        }

        if ($context->isResolved()) {
            $resolvedValue = $context->getResolvedValue();
            if (is_scalar($resolvedValue) || null === $resolvedValue || $resolvedValue instanceof Stringable) {
                return Value::success((string) $resolvedValue);
            }

            return Value::success('');
        }

        return Value::fail();
    }
}
