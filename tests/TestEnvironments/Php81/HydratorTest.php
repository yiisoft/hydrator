<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php81;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\TestEnvironments\Php81\Support\ReadonlyModel;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class HydratorTest extends TestCase
{
    public function testReadonlyModel(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = $service->create(ReadonlyModel::class, ['a' => 1, 'b' => 2, 'c' => 3]);

        $this->assertSame(99, $model->a);
        $this->assertSame(2, $model->b);
        $this->assertSame(3, $model->c);
    }
}
