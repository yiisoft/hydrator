<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Exception;

use ReflectionClass;

final class AbstractClassException extends NonInstantiableException
{
    public function __construct(ReflectionClass $reflectionClass)
    {
        parent::__construct(
            sprintf(
                '"%s" is not instantiable because it is abstract.',
                $reflectionClass->getName(),
            ),
        );
    }
}
