<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Exception;

use ReflectionMethod;

final class NonPublicConstructorException extends NonInstantiableException
{
    public function __construct(ReflectionMethod $constructor)
    {
        $type = $this->getConstructorType($constructor);
        parent::__construct(
            sprintf(
                '%s is not instantiable because contain non-public%s constructor.',
                $constructor->getDeclaringClass()->getName(),
                $type !== null ? ' (' . $type . ')' : '',
            ),
        );
    }

    private function getConstructorType(ReflectionMethod $constructor): ?string
    {
        if ($constructor->isPrivate()) {
            return 'private';
        }

        if ($constructor->isProtected()) {
            return 'protected';
        }

        return null;
    }
}
