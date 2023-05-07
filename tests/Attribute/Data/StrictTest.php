<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Data;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Attribute\Data\Strict;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArray;
use Yiisoft\Hydrator\Tests\Support\Attribute\FromPredefinedArrayResolver;
use Yiisoft\Hydrator\Tests\Support\Model\FromPredefinedArrayModel;
use Yiisoft\Hydrator\Tests\Support\Model\StrictModel;
use Yiisoft\Hydrator\UnexpectedAttributeException;
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

    public function testUnexpectedAttributeException(): void
    {
        $hydrator = new Hydrator(
            new SimpleContainer([FromPredefinedArrayResolver::class => new Strict()])
        );

        $model = new FromPredefinedArrayModel();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . Strict::class . '", but "' . FromPredefinedArray::class . '" given.');
        $hydrator->hydrate($model);
    }
}
