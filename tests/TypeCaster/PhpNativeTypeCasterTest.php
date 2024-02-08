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
            'empty string to int|null' => [
                Result::success(0),
                '',
                static fn(?int $a) => null,
            ],
            'empty string to string|null' => [
                Result::success(''),
                '',
                static fn(?string $a) => null,
            ],
            'empty string to float|null' => [
                Result::success(0.0),
                '',
                static fn(?float $a) => null,
            ],
            'empty string to bool|null' => [
                Result::success(false),
                '',
                static fn(?bool $a) => null,
            ],
            'empty string to array|null' => [
                Result::fail(),
                '',
                static fn(?array $a) => null,
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

    public function dataWithCastEmptyStringToNull(): array
    {

        return [
            'empty string to int|null' => [
                Result::success(null),
                '',
                static fn(?int $a) => null,
            ],
            'empty string to string|null' => [
                Result::success(null),
                '',
                static fn(?string $a) => null,
            ],
            'empty string to float|null' => [
                Result::success(null),
                '',
                static fn(?float $a) => null,
            ],
            'empty string to bool|null' => [
                Result::success(null),
                '',
                static fn(?bool $a) => null,
            ],
            'empty string to array|null' => [
                Result::success(null),
                '',
                static fn(?array $a) => null,
            ],
        ];
    }

    /**
     * @dataProvider dataWithCastEmptyStringToNull
     */
    public function testWithCastEmptyStringToNull(Result $expected, mixed $value, Closure $closure): void
    {
        $typeCaster = new PhpNativeTypeCaster(castEmptyStringToNull: true);
        $context = TestHelper::createTypeCastContext($closure);

        $result = $typeCaster->cast($value, $context);

        $this->assertSame($expected->isResolved(), $result->isResolved());
        $this->assertSame($expected->getValue(), $result->getValue());
    }
}
