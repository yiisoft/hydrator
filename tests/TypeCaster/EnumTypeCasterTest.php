<?php

declare(strict_types=1);

namespace TypeCaster;

use Countable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\BaseEnum;
use Yiisoft\Hydrator\Tests\Support\IntegerEnum;
use Yiisoft\Hydrator\Tests\Support\StringableObject;
use Yiisoft\Hydrator\Tests\Support\StringEnum;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Hydrator\TypeCaster\EnumTypeCaster;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;

final class EnumTypeCasterTest extends TestCase
{
    public static function dataBase(): array
    {
        return [
            'enum to not enum' => [
                Result::fail(),
                IntegerEnum::A,
                TestHelper::createTypeCastContext(static fn(int $a) => null),
            ],
            'enum to no type' => [
                Result::fail(),
                IntegerEnum::A,
                TestHelper::createTypeCastContext(static fn($a) => null),
            ],
            'enum to enum' => [
                Result::success(IntegerEnum::A),
                IntegerEnum::A,
                TestHelper::createTypeCastContext(static fn(IntegerEnum $a) => null),
            ],
            'enum to another enum' => [
                Result::fail(),
                IntegerEnum::A,
                TestHelper::createTypeCastContext(static fn(StringEnum $a) => null),
            ],
            'int to enum' => [
                Result::success(IntegerEnum::A),
                1,
                TestHelper::createTypeCastContext(static fn(IntegerEnum $a) => null),
            ],
            'int as string to enum' => [
                Result::success(IntegerEnum::A),
                '1',
                TestHelper::createTypeCastContext(static fn(IntegerEnum $a) => null),
            ],
            'stringable int to enum' => [
                Result::success(IntegerEnum::A),
                new StringableObject('1'),
                TestHelper::createTypeCastContext(static fn(IntegerEnum $a) => null),
            ],
            'invalid int to enum' => [
                Result::fail(),
                5,
                TestHelper::createTypeCastContext(static fn(IntegerEnum $a) => null),
            ],
            'string to enum' => [
                Result::success(StringEnum::B),
                'two',
                TestHelper::createTypeCastContext(static fn(StringEnum $a) => null),
            ],
            'stringable to enum' => [
                Result::success(StringEnum::B),
                new StringableObject('two'),
                TestHelper::createTypeCastContext(static fn(StringEnum $a) => null),
            ],
            'invalid string to enum' => [
                Result::fail(),
                'five',
                TestHelper::createTypeCastContext(static fn(StringEnum $a) => null),
            ],
            'enum to nulled enum' => [
                Result::success(StringEnum::B),
                'two',
                TestHelper::createTypeCastContext(static fn(?StringEnum $a) => null),
            ],
            'enum to union with enum' => [
                Result::success(StringEnum::B),
                'two',
                TestHelper::createTypeCastContext(static fn(string|StringEnum $a) => null),
            ],
            'enum to union without enum' => [
                Result::fail(),
                'two',
                TestHelper::createTypeCastContext(static fn(string|int $a) => null),
            ],
            'enum to intersection type' => [
                Result::fail(),
                IntegerEnum::A,
                TestHelper::createTypeCastContext(static fn(IntegerEnum&Countable $a) => null),
            ],
            'base enum' => [
                Result::success(BaseEnum::A),
                BaseEnum::A,
                TestHelper::createTypeCastContext(static fn(BaseEnum $a) => null),
            ],
            'base enum to union with enum' => [
                Result::success(BaseEnum::A),
                BaseEnum::A,
                TestHelper::createTypeCastContext(static fn(IntegerEnum|BaseEnum $a) => null),
            ],
            'string to base enum' => [
                Result::fail(),
                'A',
                TestHelper::createTypeCastContext(static fn(BaseEnum $a) => null),
            ],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(Result $expected, mixed $value, TypeCastContext $context): void
    {
        $typeCaster = new EnumTypeCaster();

        $result = $typeCaster->cast($value, $context);

        $this->assertSame($expected->isResolved(), $result->isResolved());
        $this->assertEquals($expected->getValue(), $result->getValue());
    }
}
