<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use ReflectionParameter;
use ReflectionProperty;

use function is_string;

/**
 * @psalm-import-type MapType from HydratorInterface
 */
final class Context
{
    /**
     * @psalm-param MapType $map
     */
    public function __construct(
        private ReflectionParameter|ReflectionProperty $parameter,
        private bool $resolved,
        private mixed $resolvedValue,
        private array $data,
        private array $map,
    ) {
    }

    public function getParameter(): ReflectionParameter|ReflectionProperty
    {
        return $this->parameter;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

    public function getResolvedValue(): mixed
    {
        return $this->resolvedValue;
    }

    /**
     * @param string|string[]|null $key
     * @throws NotResolvedException
     */
    public function getData(array|string|null $key = null): mixed
    {
        if ($key === null) {
            return $this->data;
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
