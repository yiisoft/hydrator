<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionType;
use Yiisoft\Hydrator\SkipTypeCastException;
use Yiisoft\Hydrator\TypeCasterInterface;

/**
 * Allows using many type casters one by one before the value cast successfully.
 */
final class CompositeTypeCaster implements TypeCasterInterface
{
    /**
     * @var TypeCasterInterface[] Type casters to use.
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
