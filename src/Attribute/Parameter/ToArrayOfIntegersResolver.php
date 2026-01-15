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
            $array = [];
            foreach ($resolvedValue as $value) {
                if (is_string($value) && trim($value) === '') {
                    continue;
                }

                $array[] = (int) $value;
            }
        } else {
            $value = $this->castValueToString($resolvedValue);
            if ($attribute->splitResolvedValue) {
                $array = [];
                if (trim($value) !== '') {
                    /**
                     * @var string[] $stringArray We assume valid regular expression is used here, so `preg_split()` always returns
                     * an array of strings.
                     */
                    $stringArray = preg_split('~' . $attribute->separator . '~u', $value);

                    foreach ($stringArray as $item) {
                        if (trim($item) === '') {
                            continue;
                        }

                        $array[] = (int) $item;
                    }
                }
            } elseif (trim($value) === '') {
                $array = [];
            } else {
                $array = [(int) $value];
            }
        }

        return Result::success($array);
    }

    private function castValueToString(mixed $value): string
    {
        return is_scalar($value) || $value instanceof Stringable ? (string) $value : '';
    }
}
