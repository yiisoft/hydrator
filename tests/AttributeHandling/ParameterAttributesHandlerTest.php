<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\AttributeHandling;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ReflectionAttributeResolverFactory;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Tests\Support\Attribute\ContextViewer;
use Yiisoft\Hydrator\Tests\Support\Attribute\ContextViewerResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\CustomValue;
use Yiisoft\Hydrator\Tests\Support\Attribute\NoResolveAttr;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ParameterAttributesHandlerTest extends TestCase
{
    public function testDefaultsHandleParameters(): void
    {
        $contextViewerResolver = new ContextViewerResolver();
        $handler = new ParameterAttributesHandler(
            new ContainerAttributeResolverFactory(
                new SimpleContainer([
                    ContextViewerResolver::class => $contextViewerResolver,
                ])
            ),
            new Hydrator(),
        );

        $parameter = TestHelper::getFirstParameter(static fn(#[ContextViewer] int $a) => null);

        $handler->handle($parameter);

        $context = $contextViewerResolver->getContext();
        $this->assertInstanceOf(ParameterAttributeResolveContext::class, $context);
        $this->assertFalse($context->isResolved());
        $this->assertNull($context->getResolvedValue());
    }

    public function testBase(): void
    {
        $handler = new ParameterAttributesHandler(new ReflectionAttributeResolverFactory(), new Hydrator());

        $parameter = TestHelper::getFirstParameter(static fn(#[CustomValue('42')] int $a) => null);

        $result = $handler->handle($parameter);

        $this->assertSame('42', $result->getValue());
    }

    public function testNotResolvedAttributeAfterResolved(): void
    {
        $handler = new ParameterAttributesHandler(new ReflectionAttributeResolverFactory(), new Hydrator());

        $parameter = TestHelper::getFirstParameter(
            static function(
                #[CustomValue('42')]
                #[NoResolveAttr]
                int $a
            ) {
                return null;
            }
        );

        $result = $handler->handle($parameter);

        $this->assertSame('42', $result->getValue());
    }
}
