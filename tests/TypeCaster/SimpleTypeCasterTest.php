<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TypeCaster;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\TypeCaster\SimpleTypeCaster;

final class SimpleTypeCasterTest extends TestCase
{
    public function testImmutability(): void
    {
        $hydrator = $this->createMock(HydratorInterface::class);

        $typeCaster = new SimpleTypeCaster();

        $this->assertNotSame($typeCaster, $typeCaster->withHydrator($hydrator));
    }
}
