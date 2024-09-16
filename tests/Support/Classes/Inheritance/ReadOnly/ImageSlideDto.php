<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Support\Classes\Inheritance\ReadOnly;

final class ImageSlideDto extends SlideDto
{
    public readonly string $src;
}
