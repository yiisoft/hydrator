<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Typecaster;

use ReflectionType;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\SkipTypecastException;
use Yiisoft\Hydrator\TypecasterInterface;

final class CompositeTypecaster implements TypecasterInterface
{
    /**
     * @var TypecasterInterface[]
     */
    private array $typecasters;

    public function __construct(
        TypecasterInterface ...$typecasters
    )
    {
        $this->typecasters = $typecasters;
    }

    public function cast(mixed $value, ?ReflectionType $type, Hydrator $hydrator): mixed
    {
        foreach ($this->typecasters as $typecaster) {
            try {
                return $typecaster->cast($value, $type, $hydrator);
            } catch (SkipTypecastException) {
            }
        }

        throw new SkipTypecastException();
    }
}
