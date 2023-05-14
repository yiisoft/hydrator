<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionType;
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
    ) {
        $this->typeCasters = $typeCasters;
    }

    public function cast(mixed $value, ?ReflectionType $type): mixed
    {
        foreach ($this->typeCasters as $typeCaster) {
            try {
                return $typeCaster->cast($value, $type);
            } catch (SkipTypeCastException) {
            }
        }

        throw new SkipTypeCastException();
    }
}
