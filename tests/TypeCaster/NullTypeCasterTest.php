<?php

declare(strict_types=1);

namespace TypeCaster;

use Closure;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Hydrator\TypeCaster\NullTypeCaster;

final class NullTypeCasterTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            'default, null to non-type' => [
                true,
                new NullTypeCaster(),
                null,
                fn($a) => null,
            ],
            'default, null to ?int' => [
                true,
                new NullTypeCaster(),
                null,
                fn(?int $a) => null,
            ],
            'default, null to int' => [
                false,
                new NullTypeCaster(),
                null,
                fn(int $a) => null,
            ],
            'default, empty string to ?string' => [
                false,
                new NullTypeCaster(),
                '',
                fn(?string $a) => null,
            ],
            'default, empty array to ?array' => [
                false,
                new NullTypeCaster(),
                [],
                fn(?array $a) => null,
            ],
            'default, empty array to array|string|null' => [
                false,
                new NullTypeCaster(),
                [],
                fn(array|string|null $a) => null,
            ],
            'null=false, null to non-type' => [
                false,
                new NullTypeCaster(null: false),
                null,
                fn($a) => null,
            ],
            'null=false, null to ?int' => [
                false,
                new NullTypeCaster(null: false),
                null,
                fn(?int $a) => null,
            ],
            'null=false, null to int|string|null' => [
                false,
                new NullTypeCaster(null: false),
                null,
                fn(int|string|null $a) => null,
            ],
            'emptyString=true, empty string to non-type' => [
                true,
                new NullTypeCaster(emptyString: true),
                '',
                fn($a) => null,
            ],
            'emptyString=true, empty string to ?string' => [
                true,
                new NullTypeCaster(emptyString: true),
                '',
                fn(?string $a) => null,
            ],
            'emptyString=true, empty string to string|int|null' => [
                true,
                new NullTypeCaster(emptyString: true),
                '',
                fn(string|int|null $a) => null,
            ],
            'emptyString=true, empty string to string' => [
                false,
                new NullTypeCaster(emptyString: true),
                '',
                fn(string $a) => null,
            ],
            'emptyString=true, empty string to string|int' => [
                false,
                new NullTypeCaster(emptyString: true),
                '',
                fn(string|int $a) => null,
            ],
            'emptyArray=true, empty array to non-type' => [
                true,
                new NullTypeCaster(emptyArray: true),
                [],
                fn($a) => null,
            ],
            'emptyArray=true, empty array to ?array' => [
                true,
                new NullTypeCaster(emptyArray: true),
                [],
                fn(?array $a) => null,
            ],
            'emptyArray=true, empty array to array|string|null' => [
                true,
                new NullTypeCaster(emptyArray: true),
                [],
                fn(array|string|null $a) => null,
            ],
            'emptyArray=true, empty array to array' => [
                false,
                new NullTypeCaster(emptyArray: true),
                [],
                fn(array $a) => null,
            ],
            'emptyArray=true, empty array to array|string' => [
                false,
                new NullTypeCaster(emptyArray: true),
                [],
                fn(array|string $a) => null,
            ],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(bool $success, NullTypeCaster $typeCaster, mixed $value, Closure $closure): void
    {
        $context = TestHelper::createTypeCastContext($closure);

        $result = $typeCaster->cast($value, $context);

        $this->assertSame($success, $result->isResolved());
        if ($success) {
            $this->assertNull($result->getValue());
        }
    }

    public function testConstructor(): void
    {
        $typeCaster = new NullTypeCaster();
        $context = TestHelper::createTypeCastContext(fn($a) => null);

        $result = $typeCaster->cast('hello', $context);

        $this->assertSame(false, $result->isResolved());
    }
}
