<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\Attribute\Parameter\DiResolver;
use Yiisoft\Hydrator\UnexpectedAttributeException;

final class UnexpectedAttributeExceptionTest extends TestCase
{
    public function testBase(): void
    {
        $previous = new LogicException();
        $exception = new UnexpectedAttributeException(DiResolver::class, new stdClass(), 255, $previous);

        $this->assertSame(
            'Expected "' . DiResolver::class . '", but "' . stdClass::class . '" given.',
            $exception->getMessage(),
        );
        $this->assertSame(255, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
