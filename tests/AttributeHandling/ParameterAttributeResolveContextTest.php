<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\AttributeHandling;

use LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\TestHelper;

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

    public function testGetHydratorNull(): void
    {
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success(1),
            new ArrayData(),
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Hydrator is not set in parameter attribute resolve context.');
        $context->getHydrator();
    }
}
