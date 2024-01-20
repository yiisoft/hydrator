<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TypeCaster;

use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use ReflectionNamedType;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;

final class TypeCastContextTest extends TestCase
{
    public function testGetters(): void
    {
        $hydrator = new Hydrator();
        $reflection = (new ReflectionFunction(static fn(int $a) => null))->getParameters()[0];

        $context = new TypeCastContext($hydrator, $reflection);

        $this->assertSame($reflection, $context->getReflection());
        $this->assertInstanceOf(ReflectionNamedType::class, $context->getReflectionType());
        $this->assertSame($hydrator, $context->getHydrator());
    }
}
