<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Stringable;
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
                static fn(mixed $value): int => (int) $value,
                $resolvedValue instanceof Traversable ? iterator_to_array($resolvedValue) : $resolvedValue
            );
        } else {
            $value = $this->castValueToString($resolvedValue);
            /**
             * @var string[] $stringArray We assume valid regular expression is used here, so `preg_split()` always returns
             * an array of strings.
             */
            $stringArray = $attribute->splitResolvedValue
                ? preg_split('~' . $attribute->separator . '~u', $value)
                : [$value];
            
            $array = array_map(
                static fn(mixed $value): int => (int) $value,
                $stringArray
            );
        }

        return Result::success($array);
    }

    private function castValueToString(mixed $value): string
    {
        return is_scalar($value) || $value instanceof Stringable ? (string) $value : '';
    }
}
