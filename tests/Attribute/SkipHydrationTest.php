<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Model\SkipHydrationModel;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class SkipHydrationTest extends TestCase
{
    public function testBase(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = $service->create(SkipHydrationModel::class, ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);

        $this->assertNull($model->a);
        $this->assertSame(2, $model->b);
        $this->assertNull($model->c);
        $this->assertSame(4, $model->d);
    }
}
