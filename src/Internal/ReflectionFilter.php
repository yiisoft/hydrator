<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Internal;

use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;
use Yiisoft\Hydrator\Attribute\SkipHydration;

use function in_array;

/**
 * @internal
 */
final class ReflectionFilter
{
    /**
     * @return array<string, ReflectionProperty>
     */
    public static function filterProperties(
        ReflectionClass $reflectionClass,
        array $propertyNamesToFilter = []
    ): array {
        $result = [];

        foreach ($reflectionClass->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            /** @psalm-suppress UndefinedMethod Need for PHP 8.0 only */
            if (PHP_VERSION_ID >= 80100 && $property->isReadOnly()) {
                continue;
            }
            $propertyName = $property->getName();
            if (in_array($propertyName, $propertyNamesToFilter, true)) {
                continue;
            }

            if (!empty($property->getAttributes(SkipHydration::class))) {
                continue;
            }

            $result[$propertyName] = $property;
        }
        return $result;
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @return array<string, ReflectionParameter>
     */
    public static function filterParameters(array $parameters): array
    {
        $result = [];

        foreach ($parameters as $parameter) {
            if (!empty($parameter->getAttributes(SkipHydration::class))) {
                continue;
            }

            $result[$parameter->getName()] = $parameter;
        }
        return $result;
    }
}
