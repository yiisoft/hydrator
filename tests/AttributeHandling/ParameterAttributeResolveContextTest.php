<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\AttributeHandling;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Result;

final class ParameterAttributeResolveContextTest extends TestCase
{
    public function testBase(): void
    {
        $parameter = (new ReflectionClass(
            new class () {
                private int $age = 42;
            }
        ))->getProperties()[0];
        $data = new ArrayData(['a' => 5, 'b' => 6]);

        $context = new ParameterAttributeResolveContext($parameter, Result::success(7), $data, new Hydrator());

        $this->assertSame($parameter, $context->getParameter());
        $this->assertTrue($context->isResolved());
        $this->assertSame(7, $context->getResolvedValue());
        $this->assertSame($data, $context->getData());
    }
}
