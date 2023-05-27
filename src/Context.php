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
        private Result $resolveResult,
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
        return $this->resolveResult->isResolved();
    }

    public function getResolvedValue(): mixed
    {
        return $this->resolveResult->getValue();
    }

    /**
     * @param string|string[]|null $key
     */
    public function getData(array|string|null $key = null): Result
    {
        if ($key === null) {
            return Result::success($this->data);
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
