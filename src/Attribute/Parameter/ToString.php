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

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class ToString implements ParameterAttributeInterface, ParameterAttributeResolverInterface
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
            if (is_scalar($resolvedValue) || null === $resolvedValue || $resolvedValue instanceof Stringable) {
                return (string) $resolvedValue;
            }
            return '';
        }

        throw new NotResolvedException();
    }
}
