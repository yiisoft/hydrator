<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeResolverInitiator;

use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeInterface;

interface AttributeResolverInitiatorInterface
{
    /**
     * @throws NonInitiableResolverException
     */
    public function initiate(DataAttributeInterface|ParameterAttributeInterface $attribute): mixed;
}
