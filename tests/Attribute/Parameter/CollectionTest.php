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
use Yiisoft\Hydrator\DataInterface;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\Chart;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\ChartSet;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\Coordinates;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\Point;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Hydrator\Tests\Support\Classes\Post\Post;
use Yiisoft\Hydrator\Tests\Support\Classes\Post\PostCategory;
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
        $object = new PostCategory();

        $hydrator->hydrate($object, ['post' => []]);
        $this->assertEmpty($object->getPosts());
    }

    public function testInvalidValue(): void
    {
        $hydrator = new Hydrator();
        $object = new PostCategory();

        $hydrator->hydrate($object, ['posts' => new stdClass()]);
        $this->assertEmpty($object->getPosts());
    }

    public function testInvalidValueItem(): void
    {
        $hydrator = new Hydrator();
        $object = new PostCategory();

        $hydrator->hydrate(
            $object,
            [
                'posts' => [
                    ['name' => 'Post 1'],
                    new stdClass(),
                    ['name' => 'Post 2', 'description' => 'Description for post 2'],
                ],
            ],
        );
        $this->assertEquals(
            [
                new Post(name: 'Post 1'),
                new Post(name: 'Post 2', description: 'Description for post 2'),
            ],
            $object->getPosts(),
        );
    }

    public function testNonInstantiableValueItem(): void
    {
        $hydrator = new Hydrator();
        $object = new PostCategory();

        $hydrator->hydrate(
            $object,
            [
                'posts' => [
                    ['name' => 'Post 1'],
                    ['name' => []],
                    ['name' => 'Post 2', 'description' => 'Description for post 2'],
                ],
            ],
        );
        $this->assertEquals(
            [
                new Post(name: 'Post 1'),
                new Post(name: 'Post 2', description: 'Description for post 2'),
            ],
            $object->getPosts(),
        );
    }

    public static function dataBase(): array
    {
        return [
            'basic' => [
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
            'nested, one to one and one to many relations' => [
                new Collection(Chart::class),
                [
                    [
                        'points' => [
                            ['coordinates' => ['x' => 1, 'y' => 1], 'rgb' => [255, 0, 0]],
                            ['coordinates' => ['x' => 2, 'y' => 2], 'rgb' => [255, 0, 0]],
                        ],
                    ],
                    [
                        'points' => [
                            ['coordinates' => ['x' => 3, 'y' => 3], 'rgb' => [0, 255, 0]],
                            ['coordinates' => ['x' => 4, 'y' => 4], 'rgb' => [0, 255, 0]],
                        ],
                    ],
                    [
                        'points' => [
                            ['coordinates' => ['x' => 5, 'y' => 5], 'rgb' => [0, 0, 255]],
                            ['coordinates' => ['x' => 6, 'y' => 6], 'rgb' => [0, 0, 255]],
                        ],
                    ],
                ],
                [
                    new Chart([
                        new Point(new Coordinates(1, 1), [255, 0, 0]),
                        new Point(new Coordinates(2, 2), [255, 0, 0]),
                    ]),
                    new Chart([
                        new Point(new Coordinates(3, 3), [0, 255, 0]),
                        new Point(new Coordinates(4, 4), [0, 255, 0]),
                    ]),
                    new Chart([
                        new Point(new Coordinates(5, 5), [0, 0, 255]),
                        new Point(new Coordinates(6, 6), [0, 0, 255]),
                    ]),
                ],
            ],
            'value item provided by class' => [
                new Collection(Post::class),
                [
                    ['name' => 'Post 1'],
                    new class () implements DataInterface {
                        public function getValue(string $name): Result
                        {
                            $value = $name === 'name' ? 'Post 2' : 'Description for post 2';

                            return Result::success($value);
                        }
                    },
                ],
                [
                    new Post(name: 'Post 1'),
                    new Post(name: 'Post 2', description: 'Description for post 2'),
                ],
            ],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(Collection $attribute, array $value, mixed $expectedValue): void
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
                            ['coordinates' => ['x' => 1, 'y' => 1], 'rgb' => [255, 0, 0]],
                            ['coordinates' => ['x' => 2, 'y' => 2], 'rgb' => [255, 0, 0]],
                        ],
                    ],
                    [
                        'points' => [
                            ['coordinates' => ['x' => 3, 'y' => 3], 'rgb' => [0, 255, 0]],
                            ['coordinates' => ['x' => 4, 'y' => 4], 'rgb' => [0, 255, 0]],
                        ],
                    ],
                    [
                        'points' => [
                            ['coordinates' => ['x' => 5, 'y' => 5], 'rgb' => [0, 0, 255]],
                            ['coordinates' => ['x' => 6, 'y' => 6], 'rgb' => [0, 0, 255]],
                        ],
                    ],
                ],
            ],
        );

        $this->assertEquals(
            new ChartSet([
                new Chart([
                    new Point(new Coordinates(1, 1), [255, 0, 0]),
                    new Point(new Coordinates(2, 2), [255, 0, 0]),
                ]),
                new Chart([
                    new Point(new Coordinates(3, 3), [0, 255, 0]),
                    new Point(new Coordinates(4, 4), [0, 255, 0]),
                ]),
                new Chart([
                    new Point(new Coordinates(5, 5), [0, 0, 255]),
                    new Point(new Coordinates(6, 6), [0, 0, 255]),
                ]),
            ]),
            $object,
        );
    }
}
