<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use BackedEnum;
use ReflectionEnum;
use ReflectionNamedType;
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

        $isBackedEnum = is_a($attribute->className, BackedEnum::class, true);
        /**
         * If `$isBackedEnum` is true, `$attribute->className` is `BackedEnum` class.
         * @psalm-suppress ArgumentTypeCoercion
         */
        $isStringBackedEnum = $isBackedEnum && $this->isStringBackedEnum($attribute->className);

        $collection = [];

        if ($isBackedEnum) {
            foreach ($resolvedValue as $item) {
                try {
                    /**
                     * If `$isBackedEnum` is true, `$attribute->className` is `BackedEnum` class.
                     * @psalm-suppress ArgumentTypeCoercion
                     */
                    $collection[] = $this->createBackedEnum($attribute->className, $isStringBackedEnum, $item);
                } catch (NonInstantiableException) {
                    continue;
                }
            }
        } else {
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
        }


        return Result::success($collection);
    }

    /**
     * @psalm-param class-string<BackedEnum> $className
     * @throws NonInstantiableException
     */
    private function createBackedEnum(string $className, bool $isStringBackedEnum, mixed $value): BackedEnum
    {
        if ($value instanceof $className) {
            return $value;
        }

        if (is_string($value) || is_int($value)) {
            $enum = $className::tryFrom($isStringBackedEnum ? (string) $value : (int) $value);
            if ($enum !== null) {
                return $enum;
            }
        }

        throw new NonInstantiableException();
    }

    /**
     * @psalm-param class-string<BackedEnum> $className
     */
    private function isStringBackedEnum(string $className): bool
    {
        /** @var ReflectionNamedType $backingType */
        $backingType = (new ReflectionEnum($className))->getBackingType();
        return $backingType->getName() === 'string';
    }
}
