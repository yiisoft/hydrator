<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\ParameterAttributesHandler;
use Yiisoft\Hydrator\Tests\Support\Attribute\Value;
use Yiisoft\Hydrator\Tests\Support\SkipTypeCaster;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Hydrator\TypeCaster\SimpleTypeCaster;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ParameterAttributesHandlerTest extends TestCase
{
    public function testTypeCasted(): void
    {
        $handler = new ParameterAttributesHandler(
            new SimpleContainer(),
            new SimpleTypeCaster(),
        );

        $parameter = TestHelper::getFirstParameter(static fn(#[Value('42')] int $a) => null);

        $value = $handler->handle($parameter);

        $this->assertSame(42, $value);
    }

    public function testNonTypeCasted(): void
    {
        $handler = new ParameterAttributesHandler(
            new SimpleContainer(),
            new SkipTypeCaster(),
        );

        $parameter = TestHelper::getFirstParameter(static fn(#[Value('42')] int $a) => null);

        $value = $handler->handle($parameter);

        $this->assertSame('42', $value);
    }
}
