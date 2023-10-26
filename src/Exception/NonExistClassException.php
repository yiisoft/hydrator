<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Exception;

final class NonExistClassException extends NonInstantiableException
{
    public function __construct(string $class)
    {
        parent::__construct(
            sprintf(
                'Class "%s" not exist.',
                $class
            ),
        );
    }
}
