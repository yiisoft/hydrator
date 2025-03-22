��          �   %   �      0  ?   1  g   q  1   �  9         E  �   f  ]   �     Z     b     y  f   �  D   �  N   1  H   �     �  P   �  )  +  �  U  �   �	  �   �
  Z  �  �   �  n   �  n    ?       �  l   �  m   C  O   �  -    �   /  +     -   >     l  �   �  m     �   �  �   (  -   �  �   �  )  {  �  �  �   )  �   �  Z  �  �   .  n   �                          	                                       
                                                              $data = ['header' => 'First post', 'text' => 'Hello, world!'];
 Alternatively to specifying mapping as an array, you can use `Data` attribute to define mapping inline: But the data you have has differently named keys: For nested objects mapping you can use `ObjectMap` class: Hydrator allows you to map data: In many cases, class attribute names differ from data keys you fill and/or create objects of the class with.  For example, we have a blog post class: In this case, keys absent from the map are ignored so everything should be mapped explicitly. Mapping Resolving dependencies Strict mode The annotation will instruct hydrator to get `$connection` from DI container by `importConnection` ID. This way we take `header` key for `title` and `text` key for `body`. To resolve dependencies by specific ID using DI container, use `Di` attribute: To skip hydration of a specific property, use `SkipHydration` attribute: Using attributes You can enable strict mode by passing `true` as a third argument of `ArrayData`: final class Post
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
 use Yiisoft\Hydrator\ArrayData;
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
 use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'];
$post = $hydrator->create(Post::class, new ArrayData($data, $map));
 use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'];
$post = $hydrator->create(Post::class, new ArrayData($data, $map, true));
 use \Yiisoft\Hydrator\Attribute\Parameter\Data;

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
 use \Yiisoft\Hydrator\Attribute\Parameter\Di;

class MyClass
{
    public function __construct(
        #[Di(id: 'importConnection')]
        private ConnectionInterface $connection,
    ) {}
}
 use \Yiisoft\Hydrator\Attribute\SkipHydration;

class MyClass
{
    #[SkipHydration]
    private $property;
}
 Project-Id-Version: 
PO-Revision-Date: 2025-03-01 20:39+0500
Last-Translator: Automatically generated
Language-Team: none
Language: ru
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);
X-Generator: Poedit 3.5
 $data = ['header' => 'First post', 'text' => 'Hello, world!'];
 В качестве альтернативы указанию сопоставления в виде массива, вы можете использовать атрибут `Data` для определения сопоставления внутри самого класса: Но данные, которые у вас есть, имеют другие названия ключей: Для вложенных объектов вы можете использовать класс `ObjectMap`: Гидратор позволяет вам сопоставить данные: Во многих случаях, имена атрибутов класса отличаются от ключей данных, которыми вы заполняете и/или создаете объекты класса. Например, у вас есть класс постов блога: В этом случае ключи, отсутствующие в сопоставлении, будут проигнорированы, поэтому все ключи необходимо сопоставлять явно. Маппинг (сопоставление) Разрешение зависимостей Строгий режим Аннотация указывает гидратору получить `$connection` из DI-контейнера по ID `importConnection`. Таким образом, мы берем ключ `header` для `title` и ключ `text` для `body`. Для разрешения зависимостей для конкретного ID используйте DI-контейнер через атрибут `Di`: Чтобы пропустить заполнение определенного свойства используйте атрибут `SkipHydration`: Использование атрибутов Вы можете включить строгий режим, передавая `true` как третий аргумент в `ArrayData`: final class Post
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
 use Yiisoft\Hydrator\ArrayData;
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
 use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'];
$post = $hydrator->create(Post::class, new ArrayData($data, $map));
 use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'];
$post = $hydrator->create(Post::class, new ArrayData($data, $map, true));
 use \Yiisoft\Hydrator\Attribute\Parameter\Data;

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
 use \Yiisoft\Hydrator\Attribute\Parameter\Di;

class MyClass
{
    public function __construct(
        #[Di(id: 'importConnection')]
        private ConnectionInterface $connection,
    ) {}
}
 use \Yiisoft\Hydrator\Attribute\SkipHydration;

class MyClass
{
    #[SkipHydration]
    private $property;
}
 