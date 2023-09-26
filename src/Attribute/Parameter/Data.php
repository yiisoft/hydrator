<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Attribute;
use Yiisoft\Hydrator\AttributeInfrastructure\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\AttributeInfrastructure\ParameterAttributeInterface;
use Yiisoft\Hydrator\AttributeInfrastructure\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\AttributeInfrastructure\UnexpectedAttributeException;

/**
 * Resolve value from the data array used for object hydration by key specified.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Data implements ParameterAttributeInterface, ParameterAttributeResolverInterface
{
    /**
     * @param string|string[] $key The data array key.
     */
    public function __construct(
        private array|string $key,
    ) {
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, ParameterAttributeResolveContext $context): Result
    {
        if (!$attribute instanceof self) {
            throw new UnexpectedAttributeException(self::class, $attribute);
        }

        return $context->getData($this->key);
    }

    public function getResolver(): self
    {
        return $this;
    }
}
