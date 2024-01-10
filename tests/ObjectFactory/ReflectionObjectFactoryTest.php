<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ObjectFactory;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Yiisoft\Hydrator\Exception\WrongConstructorArgumentsCountException;
use Yiisoft\Hydrator\ObjectFactory\ReflectionObjectFactory;
use Yiisoft\Hydrator\Tests\Support\StringableObject;

final class ReflectionObjectFactoryTest extends TestCase
{
    public function testWrongConstructorArgumentsCount(): void
    {
        $factory = new ReflectionObjectFactory();
        $reflection = new ReflectionClass(StringableObject::class);

        $this->expectException(WrongConstructorArgumentsCountException::class);
        $this->expectExceptionMessage(
            'Class "' . StringableObject::class . '" cannot be instantiated because it has 1 required parameters in constructor, but passed only 0.'
        );
        $factory->create($reflection, []);
    }
}
