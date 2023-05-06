<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Model\NestedModel;

final class UserModel
{
    public function __construct(
        private Name $name
    ) {
    }

    public function getName(): string
    {
        return $this->name->getFirst() . ' ' . $this->name->getLast();
    }
}
