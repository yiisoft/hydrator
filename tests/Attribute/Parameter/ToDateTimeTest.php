<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Parameter\ToDateTime;
use Yiisoft\Hydrator\Attribute\Parameter\ToDateTimeResolver;
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

final class ToDateTimeTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'DateTime' => [
            new DateTimeImmutable('04/01/2024'),
            new ToDateTime(),
            new DateTime('04/01/2024'),
        ];
        yield 'DateTimeImmutable' => [
            new DateTimeImmutable('04/01/2024'),
            new ToDateTime(),
            new DateTimeImmutable('04/01/2024'),
        ];
        yield 'string-php-format' => [
            new DateTimeImmutable('04/01/2024'),
            new ToDateTime(format: 'php:m/d/Y'),
            '04/01/2024',
        ];
        yield 'string-intl-format' => [
            new DateTimeImmutable('04/01/2024'),
            new ToDateTime(format: 'MM/dd/yyyy'),
            '04/01/2024',
        ];
        yield 'timestamp-integer' => [
            (new DateTimeImmutable())->setTimestamp(1711972838),
            new ToDateTime(),
            1711972838,
        ];
        yield 'timezone' => [
            new DateTimeImmutable('12.11.2003, 07:20', new DateTimeZone('UTC')),
            new ToDateTime(format: 'php:d.m.Y, H:i', timeZone: 'GMT+5'),
            '12.11.2003, 12:20',
        ];
        yield 'locale-ru' => [
            new DateTimeImmutable('12.11.2020, 12:20'),
            new ToDateTime(locale: 'ru'),
            '12.11.2020, 12:20',
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(DateTimeImmutable $expected, ToDateTime $attribute, mixed $value): void
    {
        $resolver = new ToDateTimeResolver();
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
            #[ToDateTime(format: 'php:d.m.Y')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['a' => '12.11.2003']);

        $this->assertInstanceOf(DateTimeImmutable::class, $object->a);
        $this->assertEquals(new DateTimeImmutable('12.11.2003'), $object->a);
    }

    public static function dataNotResolve(): iterable
    {
        yield 'invalid-string' => ['12-11-2003'];
        yield 'invalid-date' => ['30.02.2021'];
        yield 'not-supported-type' => [new stdClass()];
    }

    #[DataProvider('dataNotResolve')]
    public function testNotResolvePhpFormat(mixed $value): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[ToDateTime(format: 'php:d.m.Y')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['a' => $value]);

        $this->assertNull($object->a);
    }

    #[DataProvider('dataNotResolve')]
    public function testNotResolveIntlFormat(mixed $value): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[ToDateTime(format: 'dd.MM.yyyy')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['a' => $value]);

        $this->assertNull($object->a);
    }

    public function testNotResolvedValue(): void
    {
        $hydrator = new Hydrator();
        $object = new class () {
            #[ToDateTime(format: 'php:d.m.Y')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['b' => '12.11.2003']);

        $this->assertNull($object->a);
    }

    public function testDefaultFormat(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    ToDateTimeResolver::class => new ToDateTimeResolver(format: 'php:Y?m?d'),
                ]),
            ),
        );
        $object = new class () {
            #[ToDateTime]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['a' => '2003x11x12']);

        $this->assertEquals(new DateTimeImmutable('12.11.2003'), $object->a);
    }

    public function testOverrideDefaultTimeZone(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    ToDateTimeResolver::class => new ToDateTimeResolver(timeZone: 'GMT+3'),
                ]),
            ),
        );
        $object = new class () {
            #[ToDateTime(locale: 'ru', timeZone: 'UTC')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['a' => '12.11.2003, 12:34']);

        $this->assertEquals(new DateTimeImmutable('12.11.2003, 12:34', new DateTimeZone('UTC')), $object->a);
    }

    public function testOverrideDefaultLocale(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    ToDateTimeResolver::class => new ToDateTimeResolver(locale: 'en'),
                ]),
            ),
        );
        $object = new class () {
            #[ToDateTime(locale: 'ru')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['a' => '12.11.2003, 12:20']);

        $this->assertEquals(new DateTimeImmutable('12.11.2003, 12:20'), $object->a);
    }

    public function testOverrideDefaultFormat(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    ToDateTimeResolver::class => new ToDateTimeResolver(format: 'php:Y-m-d'),
                ]),
            ),
        );
        $object = new class () {
            #[ToDateTime(format: 'php:d.m.Y')]
            public ?DateTimeImmutable $a = null;
        };

        $hydrator->hydrate($object, ['a' => '12.11.2003']);

        $this->assertEquals(new DateTimeImmutable('12.11.2003'), $object->a);
    }

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    CounterResolver::class => new ToDateTimeResolver(),
                ]),
            ),
        );
        $object = new CounterClass();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage(
            'Expected "' . ToDateTime::class . '", but "' . Counter::class . '" given.'
        );
        $hydrator->hydrate($object);
    }

    public static function dataResultType(): iterable
    {
        yield 'immutable-to-immutable' => [
            DateTimeImmutable::class,
            static fn(DateTimeImmutable $a) => null,
            new DateTimeImmutable(),
        ];
        yield 'immutable-to-nullable-immutable' => [
            DateTimeImmutable::class,
            static fn(?DateTimeImmutable $a) => null,
            new DateTimeImmutable(),
        ];
        yield 'mutable-to-immutable' => [
            DateTimeImmutable::class,
            static fn(DateTimeImmutable $a) => null,
            new DateTime(),
        ];
        yield 'mutable-to-nullable-immutable' => [
            DateTimeImmutable::class,
            static fn(?DateTimeImmutable $a) => null,
            new DateTime(),
        ];
        yield 'string-to-immutable' => [
            DateTimeImmutable::class,
            static fn(DateTimeImmutable $a) => null,
            '12.11.2003',
        ];
        yield 'immutable-to-mutable' => [
            DateTime::class,
            static fn(DateTime $a) => null,
            new DateTimeImmutable(),
        ];
        yield 'immutable-to-nullable-mutable' => [
            DateTime::class,
            static fn(?DateTime $a) => null,
            new DateTimeImmutable(),
        ];
        yield 'mutable-to-mutable' => [
            DateTime::class,
            static fn(DateTime $a) => null,
            new DateTime(),
        ];
        yield 'mutable-to-nullable-mutable' => [
            DateTime::class,
            static fn(?DateTime $a) => null,
            new DateTime(),
        ];
        yield 'string-to-mutable' => [
            DateTime::class,
            static fn(DateTime $a) => null,
            '12.11.2003',
        ];
        yield 'immutable-to-interface' => [
            DateTimeImmutable::class,
            static fn(DateTimeInterface $a) => null,
            new DateTimeImmutable(),
        ];
        yield 'immutable-to-nullable-interface' => [
            DateTimeImmutable::class,
            static fn(?DateTimeInterface $a) => null,
            new DateTimeImmutable(),
        ];
        yield 'mutable-to-interface' => [
            DateTimeImmutable::class,
            static fn(DateTimeInterface $a) => null,
            new DateTime(),
        ];
        yield 'mutable-to-nullable-interface' => [
            DateTimeImmutable::class,
            static fn(?DateTimeInterface $a) => null,
            new DateTime(),
        ];
        yield 'string-to-interface' => [
            DateTimeImmutable::class,
            static fn(DateTimeInterface $a) => null,
            '12.11.2003',
        ];
        yield 'string-to-interface-and-mutable' => [
            DateTimeImmutable::class,
            static fn(DateTimeInterface|DateTime $a) => null,
            '12.11.2003',
        ];
        yield 'string-to-immutable-and-mutable' => [
            DateTimeImmutable::class,
            static fn(DateTimeImmutable|DateTime $a) => null,
            '12.11.2003',
        ];
        yield 'string-to-mutable-and-immutable' => [
            DateTimeImmutable::class,
            static fn(DateTime|DateTimeImmutable $a) => null,
            '12.11.2003',
        ];
        yield 'string-to-int-and-mutable' => [
            DateTime::class,
            static fn(int|DateTime $a) => null,
            '12.11.2003',
        ];
    }

    #[DataProvider('dataResultType')]
    public function testResultType(string $expected, Closure $closure, mixed $value): void
    {
        $resolver = new ToDateTimeResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter($closure),
            Result::success($value),
            new ArrayData(),
        );

        $result = $resolver->getParameterValue(new ToDateTime(format: 'php:d.m.Y'), $context);

        $this->assertTrue($result->isResolved());
        $this->assertInstanceOf($expected, $result->getValue());
    }

    #[DataProvider('dataResultType')]
    public function testResultTypeWithIntlFormat(string $expected, Closure $closure, mixed $value): void
    {
        $resolver = new ToDateTimeResolver();
        $context = new ParameterAttributeResolveContext(
            TestHelper::getFirstParameter($closure),
            Result::success($value),
            new ArrayData(),
        );

        $result = $resolver->getParameterValue(new ToDateTime(format: 'dd.MM.yyyy'), $context);

        $this->assertTrue($result->isResolved());
        $this->assertInstanceOf($expected, $result->getValue());
    }
}
