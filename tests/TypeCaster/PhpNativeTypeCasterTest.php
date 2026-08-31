<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TypeCaster;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\StringableObject;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;

final class PhpNativeTypeCasterTest extends TestCase
{
    public static function dataBase(): array
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
            'int to object|int|string' => [
                Result::success(42),
                42,
                static fn(StringableObject|int|string $a) => null,
            ],
            'string to object|int|string' => [
                Result::success('42'),
                '42',
                static fn(StringableObject|int|string $a) => null,
            ],
            'string to object|int' => [
                Result::success(42),
                '42',
                static fn(StringableObject|int $a) => null,
            ],
            'string false to bool' => [
                Result::success(false),
                'false',
                static fn(bool $a) => null,
            ],
            'string true to bool' => [
                Result::success(true),
                'true',
                static fn(bool $a) => null,
            ],
            'string zero to bool' => [
                Result::success(false),
                '0',
                static fn(bool $a) => null,
            ],
            'string one to bool' => [
                Result::success(true),
                '1',
                static fn(bool $a) => null,
            ],
            'stringable false to bool' => [
                Result::success(false),
                new StringableObject('false'),
                static fn(bool $a) => null,
            ],
            'invalid string to bool' => [
                Result::fail(),
                'foo',
                static fn(bool $a) => null,
            ],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(Result $expected, mixed $value, Closure $closure): void
    {
        $typeCaster = new PhpNativeTypeCaster();
        $context = TestHelper::createTypeCastContext($closure);

        $result = $typeCaster->cast($value, $context);

        $this->assertSame($expected->isResolved(), $result->isResolved());
        $this->assertSame($expected->getValue(), $result->getValue());
    }
}
