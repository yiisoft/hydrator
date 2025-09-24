<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\TestEnvironments\Php84\Hydrator\NoPropertyHook;

final class Book
{
    public string $title {
        set {
            $this->title = 'setter called';
        }
    }
}
