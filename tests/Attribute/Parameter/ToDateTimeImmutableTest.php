<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\ToDateTimeImmutable;
use Yiisoft\Hydrator\Attribute\Parameter\ToDateTimeImmutableResolver;
use Yiisoft\Hydrator\Attribute\Parameter\ToString;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Tests\Support\Attribute\Counter;
use Yiisoft\Hydrator\Tests\Support\Attribute\CounterResolver;
use Yiisoft\Hydrator\Tests\Support\Classes\CounterClass;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ToDateTimeImmutableTest extends TestCase
{
    public static function dataBase(): array
    {
        return [
            'DateTime' => [
                new DateTimeImmutable('04/01/2024'),
                new ToDateTimeImmutable(),
                new DateTime('04/01/2024'),
            ],
            'DateTimeImmutable' => [
                new DateTimeImmutable('04/01/2024'),
                new ToDateTimeImmutable(),
                new DateTimeImmutable('04/01/2024'),
            ],
            'string-php-format' => [
                new DateTimeImmutable('04/01/2024'),
                new ToDateTimeImmutable(format: 'php:m/d/Y'),
                '04/01/2024',
            ],
            'string-intl-format' => [
                new DateTimeImmutable('04/01/2024'),
                new ToDateTimeImmutable(format: 'MM/dd/yyyy'),
                '04/01/2024',
            ],
            'timestamp-integer' => [
                (new DateTimeImmutable())->setTimestamp(1711972838),
                new ToDateTimeImmutable(),
                1711972838
            ],
            'timezone' => [
                new DateTimeImmutable('12.11.2003, 07:20', new DateTimeZone('UTC')),
                new ToDateTimeImmutable(format: 'php:d.m.Y, H:i', timeZone: 'GMT+5'),
                '12.11.2003, 12:20'
            ],
            'locale-ru' => [
                new DateTimeImmutable('12.11.2020, 12:20'),
                new ToDateTimeImmutable(locale: 'ru'),
                '12.11.2020, 12:20'
            ],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(mixed $expected, ToDateTimeImmutable $attribute, mixed $value): void
    {
        $resolver = new ToDateTimeImmutableResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter(static fn(?DateTimeImmutable $a) => null),
            Result::success($value),
            new ArrayData(),
        );

        $result = $resolver->getParameterValue($attribute, $context);

        $this->assertTrue($result->isResolved());
        $this->assertEquals($expected, $result->getValue());
    }

    public function testWithHydrator(): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[ToDateTimeImmutable(format: 'php:d.m.Y')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['a' => '12.11.2003']);

        $this->assertEquals(new DateTimeImmutable('12.11.2003'), $object->a);
    }

    public static function dataNotResolve(): array
    {
        return [
            'invalid-string' => ['12-11-2003'],
            'invalid-date' => ['30.02.2021'],
            'not-supported-type' => [new stdClass()],
        ];
    }

    #[DataProvider('dataNotResolve')]
    public function testNotResolve(mixed $value): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[ToDateTimeImmutable(format: 'php:d.m.Y')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['a' => $value]);

        $this->assertNull($object->a);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[ToDateTimeImmutable(format: 'php:d.m.Y')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['b' => '12.11.2003']);

        $this->assertNull($object->a);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    CounterResolver::class => new ToDateTimeImmutableResolver(),
                ]),
            ),
        );
        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . ToDateTimeImmutable::class . '", but "' . Counter::class . '" given.'
        );
        $hydrator->hydrate($object);
    }
}
