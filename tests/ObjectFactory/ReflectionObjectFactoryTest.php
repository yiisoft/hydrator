<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ObjectFactory;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Yiisoft\Hydrator\Exception\EnumException;
use Yiisoft\Hydrator\Exception\WrongConstructorArgumentsCountException;
use Yiisoft\Hydrator\ObjectFactory\ReflectionObjectFactory;
use Yiisoft\Hydrator\Tests\Support\IntegerEnum;
use Yiisoft\Hydrator\Tests\Support\StringableObject;

final class ReflectionObjectFactoryTest extends TestCase
{
    public function testWrongConstructorArgumentsCount(): void
    {
        $factory = new ReflectionObjectFactory();
        $reflection = new ReflectionClass(StringableObject::class);

        $this->expectException(WrongConstructorArgumentsCountException::class);
        $this->expectExceptionMessage(
            'Class "' . StringableObject::class . '" cannot be instantiated because it has 1 required parameters in constructor, but passed only 0.',
        );
        $factory->create($reflection, []);
    }

    public function testEnum(): void
    {
        $factory = new ReflectionObjectFactory();
        $reflection = new ReflectionClass(IntegerEnum::class);

        $this->expectException(EnumException::class);
        $this->expectExceptionMessage('"' . IntegerEnum::class . '" is not instantiable because it is an enum.');
        $factory->create($reflection, []);
    }
}
