<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\AttributeInfrastructure;

use ReflectionParameter;
use ReflectionProperty;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\Internal\DataExtractor;

use Yiisoft\Hydrator\Result;

use function is_string;

/**
 * Holds attribute resolving context data.
 *
 * @psalm-import-type MapType from HydratorInterface
 */
final class ParameterAttributeResolveContext
{
    /**
     * @param ReflectionParameter|ReflectionProperty $parameter Resolved parameter or property reflection.
     * @param Result $resolveResult The resolved value object.
     * @param array $data Data array to be used for resolving.
     * @param array $map Object property names mapped to keys in the data array.
     *
     * @psalm-param MapType $map
     */
    public function __construct(
        private ReflectionParameter|ReflectionProperty $parameter,
        private Result $resolveResult,
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
     * Get whether the value for object property is resolved already.
     *
     * @return bool Whether the value for object property is resolved.
     */
    public function isResolved(): bool
    {
        return $this->resolveResult->isResolved();
    }

    /**
     * Get the resolved value.
     *
     * When value is not resolved returns `null`. But `null` can be is resolved value, use {@see isResolved()} for check
     * the value is resolved or not.
     *
     * @return mixed The resolved value.
     */
    public function getResolvedValue(): mixed
    {
        return $this->resolveResult->getValue();
    }

    /**
     * Get data array whole or item by key.
     *
     * @param string|string[]|null $key The key to get the data item for. If null, the whole data array is returned.
     * If an array, the key is treated as a path.
     *
     * @return Result The result object.
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

        return DataExtractor::getValueByPath($this->data, $path);
    }
}
