<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Yiisoft\Hydrator\Attribute\SkipHydration;

class ObjectPropertiesExtractor
{
    /**
     * @param \ReflectionProperty[] $properties
     * @return \ReflectionProperty[]
     */
    public function filterReflectionProperties(array $properties): array
    {
        $result = [];

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            /** @psalm-suppress UndefinedMethod Need for PHP 8.0 only */
            if (PHP_VERSION_ID >= 80100 && $property->isReadOnly()) {
                continue;
            }

            if (!empty($property->getAttributes(SkipHydration::class))) {
                continue;
            }

            $result[$property->getName()] = $property;
        }
        return $result;
    }

    /**
     * @param \ReflectionParameter[] $parameters
     * @return \ReflectionParameter[]
     */
    public function filterReflectionParameters(array $parameters): array
    {
        $result = [];

        foreach ($parameters as $parameter) {
            //if ($parameter->isPromoted()) {
            //    continue;
            //}

            if (!empty($parameter->getAttributes(SkipHydration::class))) {
                continue;
            }

            $result[$parameter->getName()] = $parameter;
        }
        return $result;
    }
}
