<?php

declare(strict_types=1);

namespace TestEnvironments\Php81\TypeCaster;

use Closure;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\StringableObject;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;

final class PhpNativeTypeCasterTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            'int to object|int|string' => [
                Result::success(42),
                42,
                static fn(StringableObject|int|string $a) => null
            ],
            'string to object|int|string' => [
                Result::success('42'),
                '42',
                static fn(StringableObject|int|string $a) => null
            ],
            'string to object|int' => [
                Result::success(42),
                '42',
                static fn(StringableObject|int $a) => null
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
