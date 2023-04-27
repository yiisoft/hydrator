<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Attribute\Parameter\CastToString;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Typecast\NoTypecast;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class CastToStringTest extends TestCase
{
    public function testBase(): void
    {
        $service = new Hydrator(new SimpleContainer(), new NoTypecast());

        $model = new class() {
            #[CastToString]
            public string $a = '';
        };

        $service->populate($model, ['a' => 99]);

        $this->assertSame('99', $model->a);
    }
}
