<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Yiisoft\Strings\StringHelper;

use function is_array;
use function is_string;
use function strlen;

/**
 * @internal
 */
final class DataHelper
{
    /**
     * @psalm-param string|list<string> $path
     *
     * @throws NotResolvedException
     */
    public static function getValueByPath(array $data, string|array $path): mixed
    {
        if (is_string($path)) {
            $path = StringHelper::parsePath($path);
        }

        $value = $data;
        foreach ($path as $pathKey) {
            $value = self::getValueByKey($value, $pathKey);
        }

        return $value;
    }

    /**
     * @throws NotResolvedException
     */
    private static function getValueByKey(array $data, string $pathKey): mixed
    {
        $found = false;
        $result = null;
        foreach ($data as $dataKey => $dataValue) {
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
