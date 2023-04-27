<?php
declare(strict_types=1);

namespace Yiisoft\Hydrator;

interface DataAttributeResolverInterface
{
    public function prepareData(DataAttributeInterface $attribute, Data $data): void;
}
