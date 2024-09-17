<?php

declare(strict_types=1);

namespace TestEnvironments\Php82\TypeCaster;

use Countable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\IntegerEnum;
use Yiisoft\Hydrator\Tests\Support\StringEnum;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Hydrator\TypeCaster\EnumTypeCaster;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;

final class EnumTypeCasterTest extends TestCase
{
    public static function dataBase(): array
    {
        return [
            'enum to enum|intersection' => [
                Result::success(StringEnum::A),
                StringEnum::A,
                TestHelper::createTypeCastContext(static fn(StringEnum|(IntegerEnum&Countable) $a) => null),
            ],
            'string to enum|intersection' => [
                Result::success(StringEnum::A),
                'one',
                TestHelper::createTypeCastContext(static fn(StringEnum|(IntegerEnum&Countable) $a) => null),
            ],
            'string to intersection|enum' => [
                Result::success(StringEnum::A),
                'one',
                TestHelper::createTypeCastContext(static fn((IntegerEnum&Countable)|StringEnum $a) => null),
            ],
            'int to enum|intersection' => [
                Result::success(IntegerEnum::B),
                2,
                TestHelper::createTypeCastContext(static fn(IntegerEnum|(StringEnum&Countable) $a) => null),
            ],
            'int to intersection|enum' => [
                Result::success(IntegerEnum::B),
                2,
                TestHelper::createTypeCastContext(static fn((StringEnum&Countable)|IntegerEnum $a) => null),
            ],
            'enum to (another enum)|intersection' => [
                Result::fail(),
                IntegerEnum::B,
                TestHelper::createTypeCastContext(static fn(StringEnum|(IntegerEnum&Countable) $a) => null),
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
