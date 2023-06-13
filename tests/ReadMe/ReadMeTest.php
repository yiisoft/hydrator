<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\SimpleHydrator;

final class ReadMeTest extends TestCase
{
    public function testBasicUsageNestedArray(): void
    {
        $hydrator = new SimpleHydrator();

        $car = $hydrator->create(Car::class, [
            'name' => 'Ferrari',
            'engine' => [
                'name' => 'V8',
            ],
        ]);

        $this->assertSame('Ferrari', $car->getName());
        $this->assertSame('V8', $car->getEngine()->getName());
    }

    public function testBasicUsageDotNotation(): void
    {
        $hydrator = new SimpleHydrator();

        $car = $hydrator->create(Car::class, [
            'name' => 'Ferrari',
            'engine.name' => 'V8',
        ]);

        $this->assertSame('Ferrari', $car->getName());
        $this->assertSame('V8', $car->getEngine()->getName());
    }

    public function testMapping1(): void
    {
        $hydrator = new SimpleHydrator();

        $person = $hydrator->create(Person1::class, [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertSame('John', $person->getFirstName());
        $this->assertSame('Doe', $person->getLastName());
    }

    public function testMapping2(): void
    {
        $hydrator = new SimpleHydrator();

        $person = $hydrator->create(Person2::class, [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertSame('John', $person->getFirstName());
        $this->assertSame('Doe', $person->getLastName());
    }

    public function testToString(): void
    {
        $hydrator = new SimpleHydrator();

        $money = $hydrator->create(Money::class, [
            'value' => 4200,
            'currency' => 'AMD',
        ]);

        $this->assertSame('4200', $money->getValue());
        $this->assertSame('AMD', $money->getCurrency());
    }
}
