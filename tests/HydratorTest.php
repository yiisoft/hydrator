<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Yiisoft\Hydrator\Attribute\Parameter\Data;
use Yiisoft\Hydrator\Attribute\Parameter\DiResolver;
use Yiisoft\Hydrator\Attribute\Parameter\CastToString;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArray;
use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArrayResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\InvalidParameterResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\NotResolver;
use Yiisoft\Hydrator\Tests\Support\Model\ConstructorParameterAttributesModel;
use Yiisoft\Hydrator\Tests\Support\Model\ConstructorTypeModel;
use Yiisoft\Hydrator\Tests\Support\Model\CounterModel;
use Yiisoft\Hydrator\Tests\Support\Model\FromPredefinedArrayModel;
use Yiisoft\Hydrator\Tests\Support\Model\InvalidDataResolverModel;
use Yiisoft\Hydrator\Tests\Support\Model\ObjectPropertyModel\ObjectPropertyModel;
use Yiisoft\Hydrator\Tests\Support\Model\ObjectPropertyModel\RedCar;
use Yiisoft\Hydrator\Tests\Support\Model\StaticModel;
use Yiisoft\Hydrator\Tests\Support\Model\TypeModel;
use Yiisoft\Hydrator\Tests\Support\Model\NestedModel\UserModel;
use Yiisoft\Hydrator\Tests\Support\Model\PreparePropertyModel;
use Yiisoft\Hydrator\Tests\Support\Model\SimpleModel;
use Yiisoft\Hydrator\Tests\Support\String42Typecaster;
use Yiisoft\Hydrator\Tests\Support\StringableObject;
use Yiisoft\Hydrator\Typecaster\CompositeTypecaster;
use Yiisoft\Hydrator\Typecaster\NoTypecaster;
use Yiisoft\Hydrator\Typecaster\SimpleTypecaster;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class HydratorTest extends TestCase
{
    public function testSimpleCreate(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = $service->create(
            SimpleModel::class,
            ['a' => '1', 'b' => '2', 'c' => '3'],
        );

        $this->assertSame('1', $model->getA());
        $this->assertSame('2', $model->getB());
        $this->assertSame('3', $model->getC());
    }

    public function testSimpleHydrate(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = new SimpleModel();
        $service->hydrate(
            $model,
            ['a' => '1', 'b' => '2', 'c' => '3'],
        );

        $this->assertSame('1', $model->getA());
        $this->assertSame('2', $model->getB());
        $this->assertSame('3', $model->getC());
    }

    public function testSimpleCreateStrict(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = $service->create(
            SimpleModel::class,
            ['a' => '1', 'b' => '2', 'c' => '3'],
            ['b' => 'b'],
            true,
        );

        $this->assertSame('.', $model->getA());
        $this->assertSame('2', $model->getB());
        $this->assertSame('.', $model->getC());
    }

    public function testSimpleHydrateStrict(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = new SimpleModel();
        $service->hydrate(
            $model,
            ['a' => '1', 'b' => '2', 'c' => '3'],
            ['b' => 'b'],
            true,
        );

        $this->assertSame('.', $model->getA());
        $this->assertSame('2', $model->getB());
        $this->assertSame('.', $model->getC());
    }

    public function dataSimpleHydrateWithMap(): array
    {
        return [
            'simple' => [
                ['x' => '1', 'y' => '2', 'z' => '3'],
                ['a' => 'x', 'b' => 'y', 'c' => 'z'],
            ],
            'simple-partly' => [
                ['a' => '1', 'y' => '2', 'c' => '3'],
                ['b' => 'y'],
            ],
            'simple-with-dot-in-key' => [
                ['x.inner' => '1', 'y' => '2', 'z' => '3'],
                ['a' => 'x\.inner', 'b' => 'y', 'c' => 'z'],
            ],
            'nested' => [
                ['x' => ['inner' => '1'], 'y' => '2', 'z' => '3'],
                ['a' => 'x.inner', 'b' => 'y', 'c' => 'z'],
            ],
            'nested-with-array-map' => [
                ['x' => ['inner' => '1'], 'y' => '2', 'z' => '3'],
                ['a' => ['x', 'inner'], 'b' => 'y', 'c' => 'z'],
            ],
        ];
    }

    /**
     * @dataProvider dataSimpleHydrateWithMap
     */
    public function testSimpleHydrateWithMap(array $data, array $map): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = new SimpleModel();
        $service->hydrate($model, $data, $map);

        $this->assertSame('1', $model->getA());
        $this->assertSame('2', $model->getB());
        $this->assertSame('3', $model->getC());
    }

    public function testPrepareProperty(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = $service->create(PreparePropertyModel::class, ['a' => 'test']);

        $this->assertSame('test!', $model->getA());
    }

    public function testCreateNestedModel(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = $service->create(
            UserModel::class,
            ['name.first' => 'Mike', 'name' => ['last' => 'Li']]
        );

        $this->assertSame('Mike Li', $model->getName());
    }

    public function dataCreateNestedModelWithMap(): array
    {
        return [
            [
                [
                    'fio.first' => 'Mike',
                    'fio' => ['last' => 'Li'],
                ],
                ['name' => 'fio'],
            ],
            [
                [
                    'person.fio' => [
                        'first' => 'Mike',
                    ],
                    'person.fio.last' => 'Li',
                ],
                ['name' => 'person.fio'],
            ],
        ];
    }

    /**
     * @dataProvider dataCreateNestedModelWithMap
     */
    public function testCreateNestedModelWithMap(array $data, array $map): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = $service->create(UserModel::class, $data, $map);

        $this->assertSame('Mike Li', $model->getName());
    }

    public function dataTypecast(): array
    {
        return [

            // Integer
            'int-to-int' => [
                ['int' => 42],
                ['int' => 42],
            ],
            'string-to-int' => [
                ['int' => 42],
                ['int' => '42'],
            ],
            'float-to-int' => [
                ['int' => 42],
                ['int' => 42.36],
            ],
            'bool-to-int' => [
                ['int' => 1],
                ['int' => true],
            ],
            'null-to-int' => [
                ['int' => 0],
                ['int' => null],
            ],
            'object-to-int' => [
                [],
                ['int' => new stdClass()],
            ],
            'stringable-to-int' => [
                ['int' => 99],
                ['int' => new StringableObject('99')],
            ],
            'string-with-separators-to-int' => [
                ['int' => 1_000_242],
                ['int' => '1 000 242'],
            ],
            'array-to-int' => [
                [],
                ['int' => [42]],
            ],
            'null-to-nullable-int' => [
                ['intNullable' => null],
                ['intNullable' => null],
            ],

            // String
            'int-to-string' => [
                ['string' => '42'],
                ['string' => 42],
            ],
            'string-to-string' => [
                ['string' => '42'],
                ['string' => '42'],
            ],
            'float-to-string' => [
                ['string' => '42.36'],
                ['string' => '42.36'],
            ],
            'bool-to-string' => [
                ['string' => '1'],
                ['string' => true],
            ],
            'null-to-string' => [
                ['string' => ''],
                ['string' => null],
            ],
            'object-to-string' => [
                [],
                ['string' => new stdClass()],
            ],
            'stringable-to-string' => [
                ['string' => 'hello'],
                ['string' => new StringableObject('hello')],
            ],
            'array-to-string' => [
                [],
                ['string' => ['42']],
            ],
            'null-to-nullable-string' => [
                ['stringNullable' => null],
                ['stringNullable' => null],
            ],

            // Boolean
            'bool-to-bool' => [
                ['bool' => true],
                ['bool' => true],
            ],
            'int-to-bool' => [
                ['bool' => true],
                ['bool' => 1],
            ],
            'float-to-bool' => [
                ['bool' => true],
                ['bool' => 1.1],
            ],
            'string-to-bool' => [
                ['bool' => true],
                ['bool' => '1'],
            ],
            'array-to-bool' => [
                ['bool' => true],
                ['bool' => [1, 2, 3]],
            ],
            'null-to-bool' => [
                ['bool' => false],
                ['bool' => null],
                fn(TypeModel $model) => $model->bool = true,
            ],
            'object-to-bool' => [
                ['bool' => true],
                ['bool' => new stdClass()],
            ],
            'resource-to-bool' => [
                ['bool' => true],
                ['bool' => tmpfile()],
                fn(TypeModel $model) => $model->bool = true,
            ],

            // Float
            'int-to-float' => [
                ['float' => 42.0],
                ['float' => 42],
            ],
            'string-to-float' => [
                ['float' => 42.1],
                ['float' => '42.1'],
            ],
            'float-to-float' => [
                ['float' => 42.36],
                ['float' => 42.36],
            ],
            'bool-to-float' => [
                ['float' => 1.0],
                ['float' => true],
            ],
            'null-to-float' => [
                ['float' => 0.0],
                ['float' => null],
            ],
            'object-to-float' => [
                [],
                ['float' => new stdClass()],
            ],
            'stringable-to-float' => [
                ['float' => 99.36],
                ['float' => new StringableObject('99.36')],
            ],
            'string-with-separators-to-float' => [
                ['float' => 1_000_242.25],
                ['float' => '1 000 242,25'],
            ],

            // No type
            'int-to-noType' => [
                ['noType' => 9],
                ['noType' => 9],
            ],
            'array-to-noType' => [
                ['noType' => ['hello', 'world']],
                ['noType' => ['hello', 'world']],
            ],
            'null-to-noType' => [
                ['noType' => null],
                ['noType' => null],
            ],

            // Array
            'int-to-array' => [
                [],
                ['array' => 7],
            ],
            'array-to-array' => [
                ['array' => [7]],
                ['array' => [7]],
            ],
        ];
    }

    /**
     * @dataProvider dataTypecast
     */
    public function testTypecast(array $expectedValues, array $data, ?callable $prepareCallable = null): void
    {
        $model = new TypeModel();
        if ($prepareCallable !== null) {
            $prepareCallable($model);
        }

        $service = new Hydrator(new SimpleContainer());
        $service->hydrate($model, $data);

        $expectedValues = array_merge(
            [
                'noType' => -1,
                'int' => -1,
                'intNullable' => -1,
                'string' => 'x',
                'stringNullable' => 'x',
                'bool' => false,
                'float' => -2.0,
                'array' => [-1],
            ],
            $expectedValues
        );

        $this->assertSame(
            $expectedValues,
            [
                'noType' => $model->noType,
                'int' => $model->int,
                'intNullable' => $model->intNullable,
                'string' => $model->string,
                'stringNullable' => $model->stringNullable,
                'bool' => $model->bool,
                'float' => $model->float,
                'array' => $model->array,
            ]
        );
    }

    public function dataConstructorTypecast(): array
    {
        return [
            'array-to-int' => [
                [],
                ['int' => [42]],
            ],
        ];
    }

    /**
     * @dataProvider dataConstructorTypecast
     */
    public function testConstructorTypecast(array $expectedValues, array $data): void
    {
        $hydrator = new Hydrator(new SimpleContainer());
        $model = $hydrator->create(ConstructorTypeModel::class, $data);

        $expectedValues = array_merge(
            [
                'int' => -1,
            ],
            $expectedValues
        );

        $this->assertSame(
            $expectedValues,
            [
                'int' => $model->int,
            ]
        );
    }

    public function testCustomTypecast(): void
    {
        $service = new Hydrator(
            new SimpleContainer(),
            new CompositeTypecaster(
                new String42Typecaster(),
                new SimpleTypecaster(),
            ),
        );

        $model = new TypeModel();
        $service->hydrate($model, ['string' => 'test', 'int' => 7]);

        $this->assertSame('42', $model->string);
        $this->assertSame(7, $model->int);
    }

    public function testCompositeTypecastWithoutCast(): void
    {
        $service = new Hydrator(
            new SimpleContainer(),
            new CompositeTypecaster(
                new String42Typecaster(),
                new SimpleTypecaster(),
            ),
        );

        $model = new TypeModel();
        $service->hydrate($model, ['int' => new stdClass()]);

        $this->assertSame(-1, $model->int);
    }

    public function testWithPropertyAttributeResolver(): void
    {
        $resolver = new FromPredefinedArrayResolver();

        $service = new Hydrator(
            new SimpleContainer([
                FromPredefinedArrayResolver::class => $resolver
            ]),
            new NoTypecaster()
        );

        $model = new class() {
            #[FromPredefinedArray('number')]
            #[CastToString]
            public string $a;
        };

        $resolver->setArray(['number' => 42]);

        $service->hydrate($model);

        $this->assertSame('42', $model->a);
    }

    public function testWithClassAttributeResolver(): void
    {
        $resolver = new FromPredefinedArrayResolver();

        $service = new Hydrator(
            new SimpleContainer([
                FromPredefinedArrayResolver::class => $resolver
            ]),
        );

        $model = new FromPredefinedArrayModel();

        $resolver->setArray(['a' => 42]);

        $service->hydrate($model);

        $this->assertSame('42', $model->a);
    }

    public function testConstructorParameterAttributes(): void
    {
        $service = new Hydrator(
            new SimpleContainer([
                DiResolver::class => new DiResolver(
                    new SimpleContainer(['stringable42' => new StringableObject('42')])
                )
            ]),
            typecaster: new NoTypecaster(),
        );

        $model = $service->create(ConstructorParameterAttributesModel::class, ['a' => 7]);

        $this->assertSame('7', $model->getA());
        $this->assertSame('42', $model->getString());
    }

    public function testTypecastAfterAttribute(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = new class() {
            #[Data('a')]
            public ?string $x = null;

            public function __construct(
                #[Data('b')]
                public ?string $y = null,
            ) {}
        };

        $service->hydrate(
            $model,
            data: [
                'a' => 1,
                'b' => 2,
            ],
        );

        $this->assertSame('1', $model->x);
        $this->assertSame('2', $model->y);
    }

    public function testCountParameterAttributeHandle(): void
    {
        $counterResolver = new CounterResolver();
        $service = new Hydrator(
            new SimpleContainer([CounterResolver::class => $counterResolver])
        );

        $service->create(CounterModel::class);

        $this->assertSame(1, $counterResolver->getCount('a'));
        $this->assertSame(1, $counterResolver->getCount('b'));
        $this->assertSame(1, $counterResolver->getCount('c'));
    }

    public function testObjectProperty(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $model = $hydrator->create(ObjectPropertyModel::class, ['car' => new RedCar()]);

        $this->assertSame('red', $model->car->getColor());
    }

    public function testNonExistPath(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $object = new class() {
            public ?int $value = null;
        };
        $hydrator->hydrate($object, ['a' => ['b' => 23]], ['value' => 'a.b.c']);

        $this->assertNull($object->value);
    }

    public function testInvalidParameterAttributeResolver(): void
    {
        $hydrator = new Hydrator(
            new SimpleContainer([
                NotResolver::class => new NotResolver(),
            ])
        );

        $object = new class() {
            #[InvalidParameterResolver]
            public int $value;
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Parameter attribute resolver "' .
            NotResolver::class .
            '" must implement "' .
            ParameterAttributeResolverInterface::class . '".'
        );
        $hydrator->hydrate($object);
    }

    public function testInvalidDataAttributeResolver(): void
    {
        $hydrator = new Hydrator(
            new SimpleContainer([
                NotResolver::class => new NotResolver(),
            ])
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Data attribute resolver "' .
            NotResolver::class .
            '" must implement "' .
            DataAttributeResolverInterface::class . '".'
        );
        $hydrator->create(InvalidDataResolverModel::class);
    }

    public function testStaticProperty(): void
    {
        $hydrator = new Hydrator(new SimpleContainer());

        $model = $hydrator->create(StaticModel::class, ['a' => 7, 'b' => 42]);

        $this->assertSame(7, $model->a);
        $this->assertSame(0, $model::$b);
    }
}
