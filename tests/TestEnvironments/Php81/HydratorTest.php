<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php81;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support\ReadonlyClass;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support\StringableCar;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support\TypeObject;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class HydratorTest extends TestCase
{
    public function testReadonlyObject(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = $hydrator->create(ReadonlyClass::class, ['a' => 1, 'b' => 2, 'c' => 3]);

        $this->assertSame(99, $object->a);
        $this->assertSame(2, $object->b);
        $this->assertSame(3, $object->c);
    }

    public function dataTypeCast(): array
    {
        return [
            // int|string
            'int-to-int-string' => [
                ['intString' => 42],
                ['intString' => 42],
            ],
            'string-to-int-string' => [
                ['intString' => '42'],
                ['intString' => '42'],
            ],

            // intersection type casting is not supported
            'intersection' => [
                ['intersection' => 'red car'],
                ['intersection' => new StringableCar('yellow')],
            ],
        ];
    }

    /**
     * @dataProvider dataTypeCast
     */
    public function testTypeCast(array $expectedValues, array $data): void
    {
        $object = new TypeObject();

        $hydrator = new Hydrator(new SimpleContainer());
        $hydrator->hydrate($object, $data);

        $expectedValues = array_merge(
            [
                'intString' => -1,
                'intersection' => 'red car',
            ],
            $expectedValues
        );

        $this->assertSame(
            $expectedValues,
            [
                'intString' => $object->intString,
                'intersection' => (string) $object->intersection,
            ]
        );
    }
}
