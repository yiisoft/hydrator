<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php84\Hydrator\PublicPrivateSetProperty;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;

use function PHPUnit\Framework\assertSame;

final class PublicPrivateSetPropertyTest extends TestCase
{
    public function testBase(): void
    {
        $circle = (new Hydrator())->create(Circle::class, ['radius' => 5]);

        assertSame(5, $circle->radius);
    }
}
