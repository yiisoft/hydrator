<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use InvalidArgumentException;
use Throwable;

final class UnexpectedAttributeException extends InvalidArgumentException
{
    /**
     * @param string $expectedClassName Expected class name of an attribute.
     * @param object $actualObject An actual given object that's not an instance of `$expectedClassName`.
     * @param int $code The Exception code.
     * @param Throwable|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct(
        string $expectedClassName,
        object $actualObject,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            sprintf(
                'Expected "%s", but "%s" given.',
                $expectedClassName,
                $actualObject::class
            ),
            $code,
            $previous,
        );
    }
}
