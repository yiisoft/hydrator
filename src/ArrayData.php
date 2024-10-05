<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Yiisoft\Strings\StringHelper;

use function is_array;
use function is_string;
use function strlen;

/**
 * Holds data to hydrate an object from and a map to use when populating an object.
 *
 * @psalm-type MapType=array<string,string|list<string>|Map>
 */
final class ArrayData implements DataInterface
{
    private readonly Map $map;

    /**
     * @param array $data Data to hydrate object from.
     * @param array|Map $map Object property names mapped to keys in the data array that hydrator will use when
     * hydrating an object.
     * @param bool $strict Whether to hydrate properties from the map only.
     *
     * @psalm-param Map|MapType $map
     */
    public function __construct(
        private readonly array $data = [],
        array|Map $map = [],
        private readonly bool $strict = false,
    ) {
        $this->map = is_array($map) ? new Map($map) : $map;
    }

    public function getValue(string $name): Result
    {
        if ($this->strict && !$this->map->exists($name)) {
            return Result::fail();
        }

        $path = $this->map->getPath($name) ?? $name;
        if ($path instanceof Map) {
            return $this->getValueByMap($this->data, $path);
        }

        return $this->getValueByPath($this->data, $path);
    }

    /**
     * Get an array given a map as resolved result.
     */
    private function getValueByMap(array $data, Map $map): Result
    {
        $arrayData = new self($data, $map);

        $result = [];
        foreach ($map->getNames() as $name) {
            $value = $arrayData->getValue($name);
            if ($value->isResolved()) {
                $result[$name] = $value->getValue();
            }
        }

        return Result::success($result);
    }

    /**
     * Get value from an array given a path.
     *
     * @param string|string[] $path Path to the value.
     *
     * @see StringHelper::parsePath()
     */
    private function getValueByPath(array $data, string|array $path): Result
    {
        if (is_string($path)) {
            $path = StringHelper::parsePath($path);
        }

        $result = Result::success($data);
        foreach ($path as $pathKey) {
            $currentValue = $result->getValue();
            if (!is_array($currentValue)) {
                return Result::fail();
            }
            $result = $this->getValueByKey($currentValue, $pathKey);
            if (!$result->isResolved()) {
                return $result;
            }
        }

        return $result;
    }

    /**
     * Get value from an array given a key.
     *
     * @param array $data Array to get value from.
     * @param string $pathKey Key to get value for.
     *
     * @return Result The result object.
     */
    private function getValueByKey(array $data, string $pathKey): Result
    {
        $found = false;
        $result = null;
        foreach ($data as $dataKey => $dataValue) {
            $dataKey = (string) $dataKey;

            if ($dataKey === $pathKey) {
                $found = true;
                $result = (is_array($dataValue) && is_array($result))
                    ? array_merge($result, $dataValue)
                    : $dataValue;
                continue;
            }

            $pathKeyWithDot = $pathKey . '.';
            if (str_starts_with($dataKey, $pathKeyWithDot)) {
                $found = true;
                $value = [
                    substr($dataKey, strlen($pathKeyWithDot)) => $dataValue,
                ];
                $result = is_array($result)
                    ? array_merge($result, $value)
                    : $value;
            }
        }

        return $found ? Result::success($result) : Result::fail();
    }
}
