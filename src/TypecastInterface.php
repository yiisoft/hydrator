<?php
declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionType;

interface TypecastInterface
{
    /**
     * @throws SkipTypecastException
     */
    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed;
}
