<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Exception;

final class WrongConstructorArgumentsCountException extends NonInstantiableException
{
    public function __construct(\ReflectionMethod $constructor, int $countArguments)
    {
        parent::__construct(
            sprintf(
                'Class "%s" cannot be instantiated because it has %d required parameters in constructor, but passed only %d.',
                $constructor->getDeclaringClass()->getName(),
                $constructor->getNumberOfRequiredParameters(),
                $countArguments,
            )
        );
    }
}
