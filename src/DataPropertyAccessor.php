<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

class DataPropertyAccessor
{
    public function resolve(string $name, Data $data): Result
    {
        $map = $data->getMap();

        if ($data->isStrict() && !array_key_exists($name, $map)) {
            return Result::fail();
        }

        return DataHelper::getValueByPath($data->getData(), $map[$name] ?? $name);
    }
}
