<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\Result;

final class ContextTest extends TestCase
{
    public function testBase(): void
    {
        $parameter = (new ReflectionClass(
            new class () {
                private int $age = 42;
            }
        ))->getProperties()[0];

        $context = new Context($parameter, Result::success(7), ['a' => 5, 'b' => 6], []);

        $this->assertSame($parameter, $context->getParameter());
        $this->assertTrue($context->isResolved());
        $this->assertSame(7, $context->getResolvedValue());
        $this->assertSame(5, $context->getData('a')->getValue());
        $this->assertSame(['a' => 5, 'b' => 6], $context->getData()->getValue());
    }
}
