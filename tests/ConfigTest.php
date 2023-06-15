<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\SimpleHydrator;

use function dirname;

final class ConfigTest extends TestCase
{
    public function testBase(): void
    {
        $container = new Container(
            ContainerConfig::create()->withDefinitions($this->getContainerDefinitions())
        );

        $hydrator = $container->get(HydratorInterface::class);

        $this->assertInstanceOf(SimpleHydrator::class, $hydrator);
    }

    private function getContainerDefinitions(): array
    {
        return require dirname(__DIR__) . '/config/di.php';
    }
}
