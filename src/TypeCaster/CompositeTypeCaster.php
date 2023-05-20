<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionType;
use Yiisoft\Hydrator\SkipTypeCastException;
use Yiisoft\Hydrator\TypeCasterInterface;

/**
 * Allows using multiple type casters one by one before the value is casted successfully.
 */
final class CompositeTypeCaster implements TypeCasterInterface
{
    /**
     * @var TypeCasterInterface[]
     */
    private array $typeCasters;

    /**
     * @param TypeCasterInterface ...$typeCasters Type casters to use.
     */
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
