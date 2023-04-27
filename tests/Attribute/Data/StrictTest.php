<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Data;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Model\StrictModel;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class StrictTest extends TestCase
{
    public function testBase(): void
    {
        $service = new Hydrator(new SimpleContainer());

        $model = $service->create(StrictModel::class);

        $this->assertSame('1', $model->a);
        $this->assertSame('2', $model->b);
        $this->assertSame('.', $model->c);
    }
}
