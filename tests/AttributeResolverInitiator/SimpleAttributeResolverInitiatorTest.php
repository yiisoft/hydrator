<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\AttributeResolverInitiator;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\AttributeResolverInitiator\NonInitiableResolverException;
use Yiisoft\Hydrator\AttributeResolverInitiator\SimpleAttributeResolverInitiator;
use Yiisoft\Hydrator\DataAttributeInterface;

final class SimpleAttributeResolverInitiatorTest extends TestCase
{
    public function testClassNotExists(): void
    {
        $initiator = new SimpleAttributeResolverInitiator();

        $attribute = new class () implements DataAttributeInterface {
            public function getResolver(): string
            {
                return 'non-exist';
            }
        };

        $this->expectException(NonInitiableResolverException::class);
        $this->expectExceptionMessage('Class "non-exist" does not exist.');
        $initiator->initiate($attribute);
    }
}
