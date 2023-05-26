<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionParameter;
use ReflectionProperty;

use function is_string;

/**
 * Holds attribute context data.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
final class Context
{
    /**
     * @param ReflectionParameter|ReflectionProperty $parameter Resolved parameter or property reflection.
     * @param Value $resolvedValue The resolved value object.
     * @param array $data Data to be used for resolving.
     * @param array $map Map of data keys to object property names.
     * @psalm-param MapType $map
     */
    public function __construct(
        private ReflectionParameter|ReflectionProperty $parameter,
        private Value $resolvedValue,
        private array $data,
        private array $map,
    ) {
    }

    /**
     * Get resolved parameter or property reflection.
     *
     * @return ReflectionParameter|ReflectionProperty Resolved parameter or property reflection.
     */
    public function getParameter(): ReflectionParameter|ReflectionProperty
    {
        return $this->parameter;
    }

    /**
     * Get whether the parameter or property is resolved.
     *
     * @return bool Whether the parameter or property is resolved.
     */
    public function isResolved(): bool
    {
        return $this->resolvedValue->isResolved();
    }

    /**
     * Get the resolved value.
     *
     * @return mixed The resolved value.
     */
    public function getResolvedValue(): mixed
    {
        return $this->resolvedValue->getValue();
    }

    /**
     * Get data item given the key.
     *
     * @param string|string[]|null $key The key to get the data item for. If null, the whole data array is returned.
     * If an array, the key is treated as a path.
     */
    public function getData(array|string|null $key = null): Value
    {
        if ($key === null) {
            return Value::success($this->data);
        }

        if (is_string($key)) {
            $path = $this->map[$key] ?? $key;
        } else {
            $path = implode('.', $key);
            $path = $this->map[$path] ?? $key;
        }

        return DataHelper::getValueByPath($this->data, $path);
    }
}
