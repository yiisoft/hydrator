<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\Collection;
use Yiisoft\Hydrator\Attribute\Parameter\CollectionResolver;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\Chart;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\ChartSet;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\Coordinates;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\Point;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Hydrator\Tests\Support\Classes\Post;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class CollectionTest extends TestCase
{
    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    CounterResolver::class => new CollectionResolver(),
                ]),
            ),
        );
        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . Collection::class . '", but "' . Counter::class . '" given.'
        );
        $hydrator->hydrate($object);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[Collection(Chart::class)]
            public array $charts = [];
        };

        $hydrator->hydrate($object, ['chart' => []]);
        $this->assertEmpty($object->charts);
    }

    public function testInvalidValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[Collection(Chart::class)]
            public array $charts = [];
        };

        $hydrator->hydrate($object, ['charts' => new stdClass()]);
        $this->assertEmpty($object->charts);
    }

    public function testInvalidValueItem(): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[Collection(Chart::class)]
            public array $charts = [];
        };

        $hydrator->hydrate(
            $object,
            [
                'charts' => [
                    new stdClass(),
                ],
            ],
        );
        $this->assertEquals($object->charts, [new stdClass()]);
    }

    public static function dataBase(): array
    {
        return [
            [
                new Collection(Post::class),
                [
                    ['name' => 'Post 1'],
                    ['name' => 'Post 2', 'description' => 'Description for post 2'],
                ],
                [
                    new Post(name: 'Post 1'),
                    new Post(name: 'Post 2', description: 'Description for post 2'),
                ],
            ],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(Collection $attribute, array $value, array $expectedValue): void
    {
        $resolver = new CollectionResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?string $a) => null),
            Result::success($value),
            new ArrayData(),
            new Hydrator(),
        );
        $result = $resolver->getParameterValue($attribute, $context);

        $this->assertTrue($result->isResolved());
        $this->assertEquals($expectedValue, $result->getValue());
    }

    public function testWithHydrator(): void
    {
        $hydrator = new Hydrator();
        $object = $hydrator->create(
            ChartSet::class,
            [
                'charts' => [
                    [
                        'points' => [
                            ['coordinates' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0]],
                            ['coordinates' => ['x' => -12, 'y' => 12], 'rgb' => [0, -2, 257]],
                        ],
                    ],
                    [
                        'points' => [
                            ['coordinates' => ['x' => -1, 'y' => 1], 'rgb' => [0, 0, 0]],
                            ['coordinates' => ['x' => -2, 'y' => 2], 'rgb' => [255, 255, 255]],
                        ],
                    ],
                    [
                        'points' => [
                            ['coordinates' => ['x' => -13, 'y' => 13], 'rgb' => [-3, 258, 0]],
                            ['coordinates' => ['x' => -14, 'y' => 14], 'rgb' => [0, -4, 259]],
                        ],
                    ],
                ],
            ],
        );

        $this->assertEquals(
            new ChartSet([
                new Chart([
                    new Point(new Coordinates(-11, 11), [-1, 256, 0]),
                    new Point(new Coordinates(-12, 12), [0, -2, 257]),
                ]),
                new Chart([
                    new Point(new Coordinates(-1, 1), [0, 0, 0]),
                    new Point(new Coordinates(-2, 2), [255, 255, 255]),
                ]),
                new Chart([
                    new Point(new Coordinates(-13, 13), [-3, 258, 0]),
                    new Point(new Coordinates(-14, 14), [0, -4, 259]),
                ]),
            ]),
            $object,
        );
    }
}
