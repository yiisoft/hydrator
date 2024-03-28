<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes;

use Stringable;

final class TypeClass
{
    public $noType = -1;
    public int $int = -1;
    public ?int $intNullable = -1;
    public string $string = 'x';
    public ?string $stringNullable = 'x';
    public bool $bool = false;
    public float $float = -2.0;
    public array $array = [-1];
    public array|string $arrayOrString = 'x';
    public int|string $intString = -1;
    public CarInterface&Stringable $intersection;

    public function __construct()
    {
        $this->intersection = new StringableCar('red');
    }
}
