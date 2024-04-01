<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php82;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php82\Support\ReadonlyClass;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php82\Support\TypeObject;

final class HydratorTest extends TestCase
{
    public static function dataTypeCast(): array
    {
        return [
            'union-intersection' => [
                ['unionIntersection' => 'test'],
                ['unionIntersection' => 'test'],
            ],
        ];
    }

    #[DataProvider('dataTypeCast')]
    public function testTypeCast(array $expectedValues, array $data): void
    {
        $object = new TypeObject();

        $hydrator = new Hydrator();
        $hydrator->hydrate($object, $data);

        $expectedValues = array_merge(
            [
                'unionIntersection' => '.',
            ],
            $expectedValues
        );

        $this->assertSame(
            $expectedValues,
            [
                'unionIntersection' => (string) $object->unionIntersection,
            ]
        );
    }

    public function testReadonlyClass(): void
    {
        $hydrator = new Hydrator();

        $object = $hydrator->create(ReadonlyClass::class, ['name' => 'Test', 'age' => 19]);

        $this->assertSame('Test', $object->name);
        $this->assertSame(19, $object->age);
    }
}
