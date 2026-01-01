<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Traversable;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Result;

final class ToArrayOfIntegersResolver implements ParameterAttributeResolverInterface
{
    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        ParameterAttributeResolveContext $context
    ): Result {
        if (!$attribute instanceof ToArrayOfIntegers) {
            throw new UnexpectedAttributeException(ToArrayOfIntegers::class, $attribute);
        }

        if (!$context->isResolved()) {
            return Result::fail();
        }

        $resolvedValue = $context->getResolvedValue();
        if (is_iterable($resolvedValue)) {
            $array = array_map(
                $this->castValueToInt(...),
                $resolvedValue instanceof Traversable ? iterator_to_array($resolvedValue) : $resolvedValue
            );
        } else {
            $array = [$this->castValueToInt($resolvedValue)];
        }

        return Result::success($array);
    }

    private function castValueToInt(mixed $value): int
    {
        return (int) $value;
    }
}
