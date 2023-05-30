<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ReadMe;

use Yiisoft\Hydrator\Attribute\Data\Map;

#[Map([
    'firstName' => 'first_name',
    'lastName' => 'last_name',
])]
final class Person1
{
    public function __construct(
        private string $firstName,
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
