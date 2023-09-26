<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Attribute;

use Attribute;
use Yiisoft\Hydrator\AttributeInfrastructure\ParameterAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class ContextViewer implements ParameterAttributeInterface
{
    public function getResolver(): string
    {
        return ContextViewerResolver::class;
    }
}
