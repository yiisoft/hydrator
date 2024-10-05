<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Hydrator\NestedMapping;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Map;

final class HydratorNestedMappingTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator();

        $root = $hydrator->create(
            Root::class,
            new ArrayData(
                ['key' => 'test'],
                ['nested' => new Map(['var' => 'key'])],
            ),
        );

        $this->assertSame('test', $root->nested?->var);
        $this->assertNull($root->nested->nested2);
    }

    public function testNested2(): void
    {
        $hydrator = new Hydrator();

        $root = $hydrator->create(
            Root::class,
            new ArrayData(
                ['a' => 'A', 'b' => ['b1' => 'B1'], 'c' => 'C'],
                [
                    'nested' => new Map([
                        'var' => 'a',
                        'nested2' => new Map([
                            'var1' => ['b', 'b1'],
                            'var2' => 'c',
                        ]),
                    ]),
                ],
            ),
        );

        $this->assertSame('A', $root->nested?->var);
        $this->assertSame('B1', $root->nested?->nested2?->var1);
        $this->assertSame('C', $root->nested?->nested2?->var2);
    }

    public function testWithSameKeyInData(): void
    {
        $hydrator = new Hydrator();

        $root = $hydrator->create(
            Root::class,
            new ArrayData(
                [
                    'var' => 'test',
                    'var1' => 'A',
                    'var2' => 'B',
                ],
                [
                    'nested' => new Map([
                        'nested2' => new Map([
                            'var1' => 'var',
                        ]),
                    ]),
                ],
            ),
        );

        $this->assertSame('', $root->nested?->var);
        $this->assertSame('test', $root->nested?->nested2?->var1);
        $this->assertSame('', $root->nested?->nested2?->var2);
    }

    public function testWithoutMap(): void
    {
        $hydrator = new Hydrator();

        $root = $hydrator->create(
            Root::class,
            new ArrayData(
                [
                    'nested' => [
                        'nested2' => [
                            'var1' => 'A',
                            'var2' => 'B',
                        ],
                    ],
                ],
            ),
        );

        $this->assertSame('A', $root->nested?->nested2?->var1);
        $this->assertSame('B', $root->nested?->nested2?->var2);
    }
}
