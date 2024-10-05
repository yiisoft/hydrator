<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\ObjectMap\Readme;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ObjectMap;

final class ObjectMapReadmeTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = new Hydrator();
        $data = [
            'title' => 'Hello, World!',
            'textBody' => 'Nice to meet you.',
            'htmlBody' => '<h1>Nice to meet you.</h1>',
        ];
        $map = [
            'subject' => 'title',
            'body' => new ObjectMap([
                'text' => 'textBody',
                'html' => 'htmlBody',
            ]),
        ];

        $message = $hydrator->create(Message::class, new ArrayData($data, $map));

        $this->assertSame('Hello, World!', $message->subject);
        $this->assertSame('Nice to meet you.', $message->body?->text);
        $this->assertSame('<h1>Nice to meet you.</h1>', $message->body?->html);
    }
}
