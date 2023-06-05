<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Container;

use LogicException;
use Psr\Container\NotFoundExceptionInterface;

final class DependencyNotFoundException extends LogicException implements NotFoundExceptionInterface
{
}
