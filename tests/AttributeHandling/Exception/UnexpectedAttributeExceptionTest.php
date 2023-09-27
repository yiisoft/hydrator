<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\AttributeHandling\Exception;

use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\Attribute\Parameter\DiResolver;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;

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

    public function testDefaults(): void
    {
        $exception = new UnexpectedAttributeException(DiResolver::class, new stdClass());

        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}
