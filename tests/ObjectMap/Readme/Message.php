<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ObjectMap\Readme;

final class Message
{
    public string $subject = '';
    public ?Body $body = null;
}
