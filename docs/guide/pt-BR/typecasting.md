# Typecasting

Quando os tipos PHP são definidos na classe, a conversão de tipo acontece automaticamente na criação ou hidratação do objeto:

```php
final class Lock
{
    public function __construct(
        private string $name,
        private bool $isLocked
    ) {}
}

$hydrator = new Hydrator();
$lock = $hydrator->create(Lock::class, ['name' => 'The lock', 'isLocked' => 1]);
```

## Ajustando a conversão de tipos

Você pode ajustar a conversão de tipo passando um type-caster para o hidratador:

```php
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypeCaster\CompositeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster

$typeCaster = new CompositeTypeCaster(
    new PhpNativeTypeCaster(),
    new HydratorTypeCaster(),
);
$hydrator = new Hydrator($typeCaster);
```

O conjunto acima é o usado por padrão.

Fora da caixa, os seguintes type-casters estão disponíveis:

- `CompositeTypeCaster` permite combinar vários type-casters
- `PhpNativeTypeCaster` baseados em tipos PHP definidos na classe
- `HydratorTypeCaster` converte arrays em objetos
- `NullTypeCaster` type-casters configurável para converter `null`, string vazia e array vazio para `null`
- `NoTypeCaster` não faça nada

## Sua própria conversão de tipo

Você pode definir type-casters personalizados, se necessário:

```php
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypeCaster\CompositeTypeCaster;

final class User
{
    public function __construct(
        private string $nickname
    )
    {
    }
    
    public function getNickName(): string
    {
        return $this->nickname;
    }
}

final class NickNameTypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, TypeCastContext $context): Result
    {
        $type = $context->getReflectionType();
    
        if (
            $context->getReflection()->getName() === 'author'
            && $type instanceof ReflectionNamedType
            && $type->isBuiltin()
            && $type->getName() === 'string'
            && preg_match('~^@(.*)$~', $value, $matches)
        ) {            
            $user = new User($matches[1]);
            return Result::success($user);        
        }       

        return Result::fail();
    }
}

final class Post
{
    public function __construct(
        private string $title,
        private User $author
    )
    {    
    }
    
    public function getTitle(): string 
    {
        return $this->title;    
    }
    
    public function getAuthor(): User
    {
        return $this->author;
    }
}

$typeCaster = new CompositeTypeCaster(
    // ...
    new NickNameTypeCaster(),
);
$hydrator = new Hydrator($typeCaster);

$post = $hydrator->create(Post::class, ['title' => 'Example post', 'author' => '@samdark']);
echo $post->getAuthor()->getNickName();
```

## Usando atributos

Para converter um valor para string explicitamente, você pode usar o atributo `ToString`:

```php
use \Yiisoft\Hydrator\Attribute\Parameter\ToString;

class Money
{
    public function __construct(
        #[ToString]
        private string $value,
        private string $currency,
    ) {}
}

$money = $hydrator->create(Money::class, [
    'value' => 4200,
    'currency' => 'AMD',
]);
```

Para converter um valor para o objeto `DateTimeImmutable` ou `DateTime` explicitamente, você pode usar o atributo `ToDateTime`:

```php
use DateTimeImmutable;
use Yiisoft\Hydrator\Attribute\Parameter\ToDateTime;

class Person
{
    public function __construct(
        #[ToDateTime(locale: 'ru')]
        private ?DateTimeImmutable $birthday = null,
    ) {}
}

$person = $hydrator->create(Person::class, ['birthday' => '27.01.1986']);
```

Para remover espaços em branco (ou outros caracteres) do início e/ou final de um valor de string resolvido, você pode usar os atributos
`Trim`, `LeftTrim` ou `RightTrim`:

```php
use DateTimeImmutable;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;

class Person
{
    public function __construct(
        #[Trim] // '  John ' → 'John'
        private ?string $name = null, 
    ) {}
}

$person = $hydrator->create(Person::class, ['name' => '  John ']);
```
