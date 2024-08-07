<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\DataInterface;
use Yiisoft\Hydrator\Exception\NonInstantiableException;
use Yiisoft\Hydrator\Result;

final class CollectionResolver implements ParameterAttributeResolverInterface
{
    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        ParameterAttributeResolveContext $context,
    ): Result {
        if (!$attribute instanceof Collection) {
            throw new UnexpectedAttributeException(Collection::class, $attribute);
        }

        if (!$context->isResolved()) {
            return Result::fail();
        }

        $resolvedValue = $context->getResolvedValue();
        if (!is_iterable($resolvedValue)) {
            return Result::fail();
        }

        $collection = [];
        foreach ($resolvedValue as $item) {
            if (!is_array($item) && !$item instanceof DataInterface) {
                continue;
            }

            try {
                $collection[] = $context->getHydrator()->create($attribute->className, $item);
            } catch (NonInstantiableException) {
                continue;
            }
        }

        return Result::success($collection);
    }
}
