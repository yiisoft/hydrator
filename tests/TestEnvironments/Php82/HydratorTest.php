<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php82;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php82\Support\TypeObject;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class HydratorTest extends TestCase
{
    public function dataTypeCast(): array
    {
        return [
            'union-intersection' => [
                ['unionIntersection' => 'test'],
                ['unionIntersection' => 'test'],
            ],
        ];
    }

    /**
     * @dataProvider dataTypeCast
     */
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
}
