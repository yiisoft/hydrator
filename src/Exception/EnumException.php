<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Exception;

use ReflectionClass;

use function sprintf;

/**
 * Thrown when an enum is attempted to be instantiated.
 */
final class EnumException extends NonInstantiableException
{
    public function __construct(ReflectionClass $reflectionClass)
    {
        parent::__construct(
            sprintf(
                '"%s" is not instantiable because it is an enum.',
                $reflectionClass->getName(),
            ),
        );
    }
}
