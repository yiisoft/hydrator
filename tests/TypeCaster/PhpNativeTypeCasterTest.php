<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TypeCaster;

use Closure;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;

final class PhpNativeTypeCasterTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            'string to int' => [
                Result::success(42),
                '42',
                static fn(int $a) => null,
            ],
            'string to float' => [
                Result::success(42.52),
                '42.52',
                static fn(float $a) => null,
            ],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(Result $expected, mixed $value, Closure $closure): void
    {
        $typeCaster = new PhpNativeTypeCaster();
        $context = TestHelper::createTypeCastContext($closure);

        $result = $typeCaster->cast($value, $context);

        $this->assertSame($expected->isResolved(), $result->isResolved());
        $this->assertSame($expected->getValue(), $result->getValue());
    }
}
