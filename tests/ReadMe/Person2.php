<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe;

use Yiisoft\Hydrator\Attribute\Parameter\Data;

final class Person2
{
    public function __construct(
        #[Data('first_name')]
        private string $firstName,
        #[Data('last_name')]
        private string $lastName,
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}
