<?php
declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionType;

interface TypecasterInterface
{
    /**
     * @throws SkipTypecastException
     */
    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed;
}
