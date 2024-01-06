<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\AttributeResolverFactoryInterface;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\HydratorInterface;

use function dirname;

final class ConfigTest extends TestCase
{
    public function testBase(): void
    {
        $container = new Container(
            ContainerConfig::create()->withDefinitions($this->getContainerDefinitions())
        );

        $hydrator = $container->get(HydratorInterface::class);
        $attributeResolverFactory = $container->get(AttributeResolverFactoryInterface::class);

        $this->assertInstanceOf(Hydrator::class, $hydrator);
        $this->assertInstanceOf(ContainerAttributeResolverFactory::class, $attributeResolverFactory);
    }

    private function getContainerDefinitions(): array
    {
        return require dirname(__DIR__) . '/config/di.php';
    }
}
