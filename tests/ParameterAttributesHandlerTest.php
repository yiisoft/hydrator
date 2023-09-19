<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\ParameterAttributesHandler;
use Yiisoft\Hydrator\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\ResolverFactory\ReflectionAttributeResolverFactory;
use Yiisoft\Hydrator\Tests\Support\Attribute\ContextViewer;
use Yiisoft\Hydrator\Tests\Support\Attribute\ContextViewerResolver;
use Yiisoft\Hydrator\Tests\Support\Attribute\CustomValue;
use Yiisoft\Hydrator\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class ParameterAttributesHandlerTest extends TestCase
{
    public function testDefaultsHandleParameters(): void
    {
        $contextViewerResolver = new ContextViewerResolver();
        $handler = new ParameterAttributesHandler(new ContainerAttributeResolverFactory(
            new SimpleContainer([
                ContextViewerResolver::class => $contextViewerResolver,
            ])
        ));

        $parameter = TestHelper::getFirstParameter(static fn(#[ContextViewer] int $a) => null);

        $handler->handle($parameter);

        $context = $contextViewerResolver->getContext();
        $this->assertInstanceOf(Context::class, $context);
        $this->assertFalse($context->isResolved());
        $this->assertNull($context->getResolvedValue());
    }

    public function testNonTypeCasted(): void
    {
        $handler = new ParameterAttributesHandler(new ReflectionAttributeResolverFactory());

        $parameter = TestHelper::getFirstParameter(static fn(#[CustomValue('42')] int $a) => null);

        $result = $handler->handle($parameter);

        $this->assertSame('42', $result->getValue());
    }
}
