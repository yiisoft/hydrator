<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Result;

final class CollectionResolver implements ParameterAttributeResolverInterface
{
    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        ParameterAttributeResolveContext $context,
    ): Result
    {
        if (!$attribute instanceof Collection) {
            throw new UnexpectedAttributeException(Collection::class, $attribute);
        }

        if (!$context->isResolved()) {
            return Result::fail();
        }

        $collection = [];
        foreach ($context->getResolvedValue() as $item) {
            $collection[] = $context->getHydrator()->create($attribute->className, $item);
        }

        return Result::success($collection);
    }
}
