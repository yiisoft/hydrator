<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\Chart;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\ChartSet;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\Coordinates;
use Yiisoft\Hydrator\Tests\Support\Classes\Chart\Point;

final class CollectionTest extends TestCase
{
    public function testWithHydrator(): void
    {
        $hydrator = new Hydrator();
        $object = $hydrator->create(
            ChartSet::class,
            [
                'charts' => [
                    [
                        'points' => [
                            ['coordinates' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0]],
                            ['coordinates' => ['x' => -12, 'y' => 12], 'rgb' => [0, -2, 257]],
                        ],
                    ],
                    [
                        'points' => [
                            ['coordinates' => ['x' => -1, 'y' => 1], 'rgb' => [0, 0, 0]],
                            ['coordinates' => ['x' => -2, 'y' => 2], 'rgb' => [255, 255, 255]],
                        ],
                    ],
                    [
                        'points' => [
                            ['coordinates' => ['x' => -13, 'y' => 13], 'rgb' => [-3, 258, 0]],
                            ['coordinates' => ['x' => -14, 'y' => 14], 'rgb' => [0, -4, 259]],
                        ],
                    ],
                ],
            ],
        );

        $this->assertEquals(
            new ChartSet([
                new Chart([
                    new Point(new Coordinates(-11, 11), [-1, 256, 0]),
                    new Point(new Coordinates(-12, 12), [0, -2, 257]),
                ]),
                new Chart([
                    new Point(new Coordinates(-1, 1), [0, 0, 0]),
                    new Point(new Coordinates(-2, 2), [255, 255, 255]),
                ]),
                new Chart([
                    new Point(new Coordinates(-13, 13), [-3, 258, 0]),
                    new Point(new Coordinates(-14, 14), [0, -4, 259]),
                ]),
            ]),
            $object,
        );
    }
}
