<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TypeCaster;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\PrivateConstructorObject;
use Yiisoft\Hydrator\Tests\Support\StringableObject;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;

final class HydratorTypeCasterTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            'not array to not array' => [
                Result::fail(),
                'not array',
                TestHelper::createTypeCastContext(static fn(int $a) => null),
            ],
            'array to not array' => [
                Result::fail(),
                [5],
                TestHelper::createTypeCastContext(static fn(int $a) => null),
            ],
            'array to without type' => [
                Result::fail(),
                [5],
                TestHelper::createTypeCastContext(static fn($a) => null),
            ],
            'array to object' => [
                Result::success(new StringableObject('hello')),
                ['string' => 'hello'],
                TestHelper::createTypeCastContext(static fn(StringableObject $object) => null),
            ],
            'array to union type object|int' => [
                Result::success(new StringableObject('hello')),
                ['string' => 'hello'],
                TestHelper::createTypeCastContext(static fn(StringableObject|int $object) => null),
            ],
            'array to union type int|object' => [
                Result::success(new StringableObject('hello')),
                ['string' => 'hello'],
                TestHelper::createTypeCastContext(static fn(int|StringableObject $object) => null),
            ],
            'array to non-instantiable object' => [
                Result::fail(),
                ['string' => 'hello'],
                TestHelper::createTypeCastContext(static fn(PrivateConstructorObject $object) => null),
            ],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(Result $expected, mixed $value, TypeCastContext $context): void
    {
        $typeCaster = new HydratorTypeCaster();

        $result = $typeCaster->cast($value, $context);

        $this->assertSame($expected->isResolved(), $result->isResolved());
        $this->assertEquals($expected->getValue(), $result->getValue());
    }
}
