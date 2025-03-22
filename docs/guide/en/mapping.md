# Mapping

In many cases1, class attribute names differ from data keys you fill and/or create objects of the class with.
For example, we have a blog post class:

```php
final class Post
{
    public function __construct(
        private string $title,
        private string $body,
    ) {        
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
```

But the data you have has differently named keys:

```php
$data = ['header' => 'First post', 'text' => 'Hello, world!'];
```

Hydrator allows you to map data:

```php
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'];
$post = $hydrator->create(Post::class, new ArrayData($data, $map));
```

This way we take `header` key for `title` and `text` key for `body`.

For nested objects mapping you can use `ObjectMap` class:

```php
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ObjectMap;

final class Message {
    public string $subject = '';
    public ?Body $body = null;
}

final class Body {
    public string $text = '';
    public string $html = '';
}

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
```

## Strict mode

You can enable strict mode by passing `true` as a third argument of `ArrayData`:

```php
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'];
$post = $hydrator->create(Post::class, new ArrayData($data, $map, true));
```

In this case, keys absent from the map are ignored so everything should be mapped explicitly.

## Using attributes

Alternatively to specifying mapping as an array, you can use `Data` attribute to define mapping inline:

```php
use \Yiisoft\Hydrator\Attribute\Parameter\Data;

final class Person
{
    public function __construct(
        #[Data('first_name')]
        private string $firstName,
        #[Data('last_name')]
        private string $lastName,
    ) {}
}

$person = $hydrator->create(Person::class, [
    'first_name' => 'John',
    'last_name' => 'Doe',
]);
```

To skip hydration of a specific property, use `SkipHydration` attribute:

```php
use \Yiisoft\Hydrator\Attribute\SkipHydration;

class MyClass
{
    #[SkipHydration]
    private $property;
}
```

## Resolving dependencies

To resolve dependencies by specific ID using DI container, use `Di` attribute:

```php
use \Yiisoft\Hydrator\Attribute\Parameter\Di;

class MyClass
{
    public function __construct(
        #[Di(id: 'importConnection')]
        private ConnectionInterface $connection,
    ) {}
}
```

The annotation will instruct hydrator to get `$connection` from DI container by `importConnection` ID.
