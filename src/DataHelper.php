<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Yiisoft\Strings\StringHelper;

use function is_array;
use function is_string;
use function strlen;

/**
 * A set of static methods to work with data.
 *
 * @internal
 */
final class DataHelper
{
    /**
     * Get value from an array given a path.
     *
     * @param string|string[] $path Path to the value.
     *
     * @throws NotResolvedException
     * @see StringHelper::parsePath()
     */
    public static function getValueByPath(array $data, string|array $path): mixed
    {
        if (is_string($path)) {
            $path = StringHelper::parsePath($path);
        }

        $value = $data;
        foreach ($path as $pathKey) {
            if (!is_array($value)) {
                throw new NotResolvedException();
            }
            $value = self::getValueByKey($value, $pathKey);
        }

        return $value;
    }

    /**
     * Get value from an array given a key.
     *
     * @param array $data Array to get value from.
     * @param string $pathKey Key to get value for.
     *
     * @throws NotResolvedException
     */
    private static function getValueByKey(array $data, string $pathKey): mixed
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

        if (!$found) {
            throw new NotResolvedException();
        }

        return $result;
    }
}
