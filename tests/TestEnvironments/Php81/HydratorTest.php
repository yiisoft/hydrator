<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php81;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support\ReadonlyModel;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support\StringableCar;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support\TypeModel;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class HydratorTest extends TestCase
{
    public function testReadonlyModel(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = $service->create(ReadonlyModel::class, ['a' => 1, 'b' => 2, 'c' => 3]);

        $this->assertSame(99, $model->a);
        $this->assertSame(2, $model->b);
        $this->assertSame(3, $model->c);
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
        $model = new TypeModel();

        $service = new Hydrator(new SimpleContainer());
        $service->hydrate($model, $data);

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
                'intString' => $model->intString,
                'intersection' => (string) $model->intersection,
            ]
        );
    }
}
