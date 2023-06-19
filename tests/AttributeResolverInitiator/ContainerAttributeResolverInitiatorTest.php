<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\AttributeResolverInitiator;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\AttributeResolverInitiator\ContainerAttributeResolverInitiator;
use Yiisoft\Hydrator\AttributeResolverInitiator\NonInitiableResolverException;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ContainerAttributeResolverInitiatorTest extends TestCase
{
    public function testNonExist(): void
    {
        $initiator = new ContainerAttributeResolverInitiator(
            new SimpleContainer(),
        );

        $attribute = new class() implements DataAttributeInterface {
            public function getResolver(): string
            {
                return 'non-exist';
            }
        };

        $this->expectException(NonInitiableResolverException::class);
        $this->expectExceptionMessage('Object with identifier "non-exist" not found in the container.');
        $initiator->initiate($attribute);
    }
}
