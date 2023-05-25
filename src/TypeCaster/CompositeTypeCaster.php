<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\TypeCaster;

use ReflectionType;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Hydrator\TypeCastResult;

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

    public function cast(mixed $value, ?ReflectionType $type): TypeCastResult
    {
        foreach ($this->typeCasters as $typeCaster) {
            $result = $typeCaster->cast($value, $type);
            if ($result->isCasted()) {
                return $result;
            }
        }

        return TypeCastResult::skip();
    }
}
