<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Typecast;

use ReflectionType;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\SkipTypecastException;
use Yiisoft\Hydrator\TypecastInterface;

final class CompositeTypecast implements TypecastInterface
{
    /**
     * @var TypecastInterface[]
     */
    private array $typecasts;

    public function __construct(
        TypecastInterface ...$typecasts
    )
    {
        $this->typecasts = $typecasts;
    }

    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed
    {
        foreach ($this->typecasts as $typecast) {
            try {
                return $typecast->cast($value, $type, $hydrator);
            } catch (SkipTypecastException) {
            }
        }

        throw new SkipTypecastException();
    }
}
