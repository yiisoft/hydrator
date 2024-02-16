# Маппинг (сопоставление)

Во многих случаях, имена атрибутов класса отличаются от ключей данных, которыми вы заполняете и/или создаете объекты класса.
Например, у вас есть класс постов блога:

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

Но данные, которые у вас есть, имеют другие названия ключей:

```php
$data = ['header' => 'First post', 'text' => 'Hello, world!'];
```

Гидратор позволяет вам сопоставить данные: 

```php
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'];
$post = $hydrator->create(Post::class, new ArrayData($data, $map));
```

Таким образом, мы берем ключ `header` для `title` и ключ `text` для `body`.

## Строгий режим

Вы можете включить строгий режим, передавая `true` как третий аргумент в `ArrayData`:

```php
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'],;
$post = $hydrator->create(Post::class, new ArrayData($data, $map, true));
```

В этом случае ключи, отсутствующие в сопоставлении, будут проигнорированы, поэтому все ключи необходимо сопоставлять явно.

## Использование атрибутов

В качестве альтернативы указанию сопоставления в виде массива, вы можете использовать атрибут `Data` для определения сопоставления внутри самого класса:

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

Чтобы пропустить заполнение определенного свойства используйте атрибут `SkipHydration`:

```php
use \Yiisoft\Hydrator\Attribute\SkipHydration;

class MyClass
{
    #[SkipHydration]
    private $property;
}
```

## Разрешение зависимостей

Для разрешения зависимостей для конкретного ID используйте DI-контейнер через атрибут `Di`:

```php
ues \Yiisoft\Hydrator\Attribute\Parameter\Di;

class MyClass
{
    public function __construct(
        #[Di(id: 'importConnection')]
        private ConnectionInterface $connection,
    ) {}
}
```

Аннотация указывает гидратору получить `$connection` из DI-контейнера по ID `importConnection`.
