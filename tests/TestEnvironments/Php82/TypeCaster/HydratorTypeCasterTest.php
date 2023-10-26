<?php

declare(strict_types=1);

namespace TypeCaster;

use Closure;
use Countable;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use Stringable;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\StringableObject;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;

final class HydratorTypeCasterTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            'array to intersection type' => [
                Result::fail(),
                ['string' => 'hello'],
                $this->createContext(static fn(Stringable&Countable $object) => null),
            ],
            'array to union type with intersection type' => [
                Result::success(new StringableObject('hello')),
                ['string' => 'hello'],
                $this->createContext(static fn(StringableObject|(Stringable&Countable) $object) => null),
            ],
            'incompatible array to union type with intersection type' => [
                Result::fail(),
                ['var' => 'hello'],
                $this->createContext(static fn(StringableObject|(Stringable&Countable) $object) => null),
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

    private function createContext(Closure $fn): TypeCastContext
    {
        return new TypeCastContext(
            new Hydrator(),
            (new ReflectionFunction($fn))->getParameters()[0],
        );
    }
}
