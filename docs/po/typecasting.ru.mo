��    (      \  5   �      p     q  �   �  9     (   T  k   }  G   �  �   1     �     �  H   �     :  �   K  l   �  E   ^  -   �     �     �  <   �  -   2  ,   `  %   �  d   �  C   	     \	     o	  
   |	  !   �	  M   �	  �   �	  U   �
  @   �
  �   .  �    ,  �      �   ,  L  �  O    "  d  n  �  $   �  �     l   �  \   M  �   �  �   Z  �   �  2   �       �   "  -   �  �   �  �   �   �   �!  �   4"  ?   �"     #  �   #  T   �#  N   $  L   P$  �   �$  �   =%     �%     �%  
   �%  !   �%  �   &  #  �&  �   �'  �   u(  �   )  �  �)  ,  �+    �,  �    .  L  �.  O  �/  "  86     %       
            !   '                          	                                                                   &       "   (       $                         #              Attribute parameters: Hydrator supports collections via `Collection` attribute. The class name of related collection must be specified:                                
 Out of the box, the following type-casters are available: The set above is what's used by default. To cast a value to `DateTimeImmutable` or `DateTime` object explicitly, you can use `ToDateTime` attribute: To cast a value to string explicitly, you can use `ToString` attribute: To strip whitespace (or other characters) from the beginning and/or end of a resolved string value, you can use `Trim`, `LeftTrim` or `RightTrim` attributes: Tweaking type-casting Typecasting Use `ToArrayOfStrings` attribute to cast a value to an array of strings: Using attributes Value of `tags` will be cast to an array of strings by splitting it by `,`. For example, string `news,city,hot` will be converted to array `['news', 'city', 'hot']`. When PHP types are defined in the class, type-casting automatically happens on object creation or hydration: You can adjust type-casting by passing a type-caster to the hydrator: You can define custom type-casters if needed: Your own type-casting `Collection` `CompositeTypeCaster` allows combining multiple type-casters `EnumTypeCaster` casts values to enumerations `HydratorTypeCaster` casts arrays to objects `NoTypeCaster` does not cast anything `NullTypeCaster` configurable type caster for casting `null`, empty string and empty array to `null` `PhpNativeTypeCaster` casts based on PHP types defined in the class `ToArrayOfStrings` `ToDatetime` `ToString` `Trim` / `LeftTrim` / `RightTrim` `removeEmpty` — remove empty strings from array (boolean, default `false`); `separator` — the boundary string (default, `\R`), it's a part of regular expression so should be taken into account or properly escaped with `preg_quote()`. `splitResolvedValue` — split resolved value by separator (boolean, default `true`); `trim` — trim each string of array (boolean, default `false`); final class Lock
{
    public function __construct(
        private string $name,
        private bool $isLocked
    ) {}
}

$hydrator = new Hydrator();
$lock = $hydrator->create(Lock::class, ['name' => 'The lock', 'isLocked' => 1]);
 final class PostCategory
{
    public function __construct(
        #[Collection(Post::class)]
        private array $posts = [],
    ) {
    }
}

final class Post
{
    public function __construct(
        private string $name,
        private string $description = '',
    ) {
    }
}

$category = $hydrator->create(
    PostCategory::class,
    [
        ['name' => 'Post 1'],
        ['name' => 'Post 2', 'description' => 'Description for post 2'],
    ],
);
 use DateTimeImmutable;
use Yiisoft\Hydrator\Attribute\Parameter\ToDateTime;

class Person
{
    public function __construct(
        #[ToDateTime(locale: 'ru')]
        private ?DateTimeImmutable $birthday = null,
    ) {}
}

$person = $hydrator->create(Person::class, ['birthday' => '27.01.1986']);
 use DateTimeImmutable;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;

class Person
{
    public function __construct(
        #[Trim] // '  John ' → 'John'
        private ?string $name = null, 
    ) {}
}

$person = $hydrator->create(Person::class, ['name' => '  John ']);
 use Yiisoft\Hydrator\Attribute\Parameter\ToArrayOfStrings;

final class Post
{
    #[ToArrayOfStrings(separator: ',')]
    public array $tags = [];    
}
 use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypeCaster\CompositeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster;

$typeCaster = new CompositeTypeCaster(
    new PhpNativeTypeCaster(),
    new HydratorTypeCaster(),
);
$hydrator = new Hydrator($typeCaster);
 use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
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
 use \Yiisoft\Hydrator\Attribute\Parameter\ToString;

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
 Project-Id-Version: 
PO-Revision-Date: 2025-03-01 13:41+0500
Last-Translator: Automatically generated
Language-Team: none
Language: ru
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);
X-Generator: Poedit 3.5
 Параметры атрибута: Гидратор поддерживает коллекции через атрибут `Collection`. Необходимо указать имя класса связанной коллекции:
 Из коробки доступны следующие классы для приведения типов: Приведенный выше набор используется по-умолчанию. Чтобы явно привести значение к объекту `DateTimeImmutable` или `DateTime`, можно использовать атрибут `ToDateTime`: Для приведения значения к строке явно, вы можете использовать атрибут `ToString`: Для удаления пробелов (или других символов) из начала и/или конца строки, вы можете использовать атрибуты `Trim`, `LeftTrim` или `RightTrim`: Настройка приведения типов Приведение типов  Используйте атрибут `ToArrayOfStrings` для приведения значения к массиву строк: Использование атрибутов Значение `tags` будет приведено к массиву строк, разделенных `,`. Например, строка `news,city,hot` будет приведена к массиву `['news', 'city', 'hot']`. Когда PHP типы определены в классе, приведение типов автоматически применяется при создании или наполнении объекта: Вы можете регулировать приведение типов, передавая объект приведения типов в гидратор: При необходимости, вы можете определить пользовательский класс для приведения типов: Ваше собственное приведение типов `Collection` `CompositeTypeCaster` позволяет комбинировать несколько классов для приведения типов `EnumTypeCaster` приведение значений к перечислениям `HydratorTypeCaster` приведение массивов к объектам `NoTypeCaster` не использовать приведение типов `NullTypeCaster` настраиваемый класс для приведения `null`, пустой строки и пустого массива к `null` `PhpNativeTypeCaster` приведение типов, основанное на PHP типах, определенных в классе `ToArrayOfStrings` `ToDatetime` `ToString` `Trim` / `LeftTrim` / `RightTrim` `removeEmpty` — удаление пустой строки из массива (логическое значение, по умолчанию `false`); `separator` — символ перевода строки (по умолчанию, `\R`). Это часть регулярного выражения, поэтому ее следует учитывать или правильно экранировать с помощью `preg_quote()`. `splitResolvedValue` — разделить значения по разделителю (логическое значение, по умолчанию `true`); `trim` — обрезка каждой строки массива (логическое значение, по умолчанию `false`); final class Lock
{
    public function __construct(
        private string $name,
        private bool $isLocked
    ) {}
}

$hydrator = new Hydrator();
$lock = $hydrator->create(Lock::class, ['name' => 'The lock', 'isLocked' => 1]);
 final class PostCategory
{
    public function __construct(
        #[Collection(Post::class)]
        private array $posts = [],
    ) {
    }
}

final class Post
{
    public function __construct(
        private string $name,
        private string $description = '',
    ) {
    }
}

$category = $hydrator->create(
    PostCategory::class,
    [
        ['name' => 'Post 1'],
        ['name' => 'Post 2', 'description' => 'Description for post 2'],
    ],
);
 use DateTimeImmutable;
use Yiisoft\Hydrator\Attribute\Parameter\ToDateTime;

class Person
{
    public function __construct(
        #[ToDateTime(locale: 'ru')]
        private ?DateTimeImmutable $birthday = null,
    ) {}
}

$person = $hydrator->create(Person::class, ['birthday' => '27.01.1986']);
 use DateTimeImmutable;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;

class Person
{
    public function __construct(
        #[Trim] // '  John ' → 'John'
        private ?string $name = null, 
    ) {}
}

$person = $hydrator->create(Person::class, ['name' => '  John ']);
 use Yiisoft\Hydrator\Attribute\Parameter\ToArrayOfStrings;

final class Post
{
    #[ToArrayOfStrings(separator: ',')]
    public array $tags = [];    
}
 use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypeCaster\CompositeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster;

$typeCaster = new CompositeTypeCaster(
    new PhpNativeTypeCaster(),
    new HydratorTypeCaster(),
);
$hydrator = new Hydrator($typeCaster);
 use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
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
 use \Yiisoft\Hydrator\Attribute\Parameter\ToString;

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
 