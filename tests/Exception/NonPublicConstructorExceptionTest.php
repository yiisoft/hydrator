<?php

declare(strict_types=1);

namespace Exception;

use LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Yiisoft\Hydrator\Exception\NonPublicConstructorException;

final class NonPublicConstructorExceptionTest extends TestCase
{
    public function testWithPublicConstructor(): void
    {
        $reflection = (new ReflectionObject(
            new class() {
                public function __construct()
                {
                }
            }
        ))->getConstructor();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Exception "NonPublicConstructorException" can be used only for non-public constructors.'
        );
        new NonPublicConstructorException($reflection);
    }
}
