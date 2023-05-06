<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionType;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\SkipTypeCastException;
use Yiisoft\Hydrator\TypeCasterInterface;

final class CompositeTypeCaster implements TypeCasterInterface
{
    /**
     * @var TypeCasterInterface[]
     */
    private array $typeCasters;

    public function __construct(
        TypeCasterInterface ...$typeCasters
    )
    {
        $this->typeCasters = $typeCasters;
    }

    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed
    {
        foreach ($this->typeCasters as $typeCaster) {
            try {
                return $typeCaster->cast($value, $type, $hydrator);
            } catch (SkipTypeCastException) {
            }
        }

        throw new SkipTypeCastException();
    }
}
