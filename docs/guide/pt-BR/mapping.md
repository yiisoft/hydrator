# Mapeamento

Em muitos casos, os nomes dos atributos de classe diferem das chaves de dados com as quais você preenche e/ou cria objetos da classe.
Por exemplo, temos uma classe de postagem de blog:

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

Mas os dados que você possui têm chaves com nomes diferentes:

```php
$data = ['header' => 'First post', 'text' => 'Hello, world!'];
```

O Hydrator permite mapear os dados:

```php
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'];
$post = $hydrator->create(Post::class, new ArrayData($data, $map));
```

Desta forma, pegamos a chave `header` para `title` e a chave `text` para `body`.

Para mapeamento de objetos aninhados, você pode usar a classe `ObjectMap`:

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

## Strict mode (Modo estrito)

Você pode ativar o modo estrito passando `true` como terceiro argumento de `ArrayData`:

```php
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ArrayData;

$hydrator = new Hydrator();

$map = ['title' => 'header', 'body' => 'text'],;
$post = $hydrator->create(Post::class, new ArrayData($data, $map, true));
```

Neste caso, as chaves ausentes do mapa são ignoradas, portanto tudo deve ser mapeado explicitamente.

## Usando atributos

Alternativamente à especificação do mapeamento como um array, você pode usar o atributo `Data` para definir o mapeamento inline:

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

Para pular a hidratação de uma propriedade específica, use o atributo `SkipHydration`:

```php
use \Yiisoft\Hydrator\Attribute\SkipHydration;

class MyClass
{
    #[SkipHydration]
    private $property;
}
```

## Resolvendo dependências

Para resolver dependências por ID específico usando o contêiner DI, use o atributo `Di`:

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

A anotação instruirá o hydrator a obter `$connection` do contêiner DI pelo ID `importConnection`.
