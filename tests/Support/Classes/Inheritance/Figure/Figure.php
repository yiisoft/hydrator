<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\Inheritance\Figure;

class Figure
{
    public ?string $name = null;
    protected ?string $color = null;
    private ?int $id = null;

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
