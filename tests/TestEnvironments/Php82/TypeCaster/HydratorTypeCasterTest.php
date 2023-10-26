<?php

declare(strict_types=1);

namespace TypeCaster;

use Closure;
use Countable;
use PHPUnit\Framework\TestCase;
use Stringable;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\StringableObject;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster;

final class HydratorTypeCasterTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            'array to intersection type' => [
                Result::fail(),
                ['string' => 'hello'],
                static fn(Stringable&Countable $object) => null,
            ],
            'array to object|intersection' => [
                Result::success(new StringableObject('hello')),
                ['string' => 'hello'],
                static fn(StringableObject|(Stringable&Countable) $object) => null,
            ],
            'array to intersection|object' => [
                Result::success(new StringableObject('hello')),
                ['string' => 'hello'],
                static fn((Stringable&Countable)|StringableObject $object) => null,
            ],
            'incompatible array to object|intersection' => [
                Result::fail(),
                ['var' => 'hello'],
                static fn(StringableObject|(Stringable&Countable) $object) => null,
            ],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(Result $expected, mixed $value, Closure $closure): void
    {
        $typeCaster = new HydratorTypeCaster();
        $context = TestHelper::createTypeCastContext($closure);

        $result = $typeCaster->cast($value, $context);

        $this->assertSame($expected->isResolved(), $result->isResolved());
        $this->assertEquals($expected->getValue(), $result->getValue());
    }
}
