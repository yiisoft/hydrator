<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Data;

final class DataTest extends TestCase
{
    public function testDefaults(): void
    {
        $data = new Data();

        $this->assertSame([], $data->getData());
        $this->assertSame([], $data->getMap());
        $this->assertFalse($data->isStrict());
    }
}
