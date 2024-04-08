# Uso geral

Para hidratar um objeto existente:

```php
use Yiisoft\Hydrator\Hydrator;

$hydrator = new Hydrator();
$hydrator->hydrate($object, $data);
```

Para criar um novo objeto e preenchê-lo com os dados:

```php
use Yiisoft\Hydrator\Hydrator;

$hydrator = new Hydrator();
$object = $hydrator->create(MyClass::class, $data);
```

Para passar argumentos para o construtor de um objeto aninhado, use array aninhado ou notação de ponto:

```php
final class Engine
{
    public function __construct(
        private string $name,
    ) {}
}

final class Car
{
    public function __construct(
        private string $name,
        private Engine $engine,
    ) {}
}

// nested array
$object = $hydrator->create(Car::class, [
    'name' => 'Ferrari',
    'engine' => [
        'name' => 'V8',
    ]
]);

// or dot-notation
$object = $hydrator->create(Car::class, [
    'name' => 'Ferrari',
    'engine.name' => 'V8',
]);
```

Isso passaria o argumento construtor `name` do objeto `Car` e criaria um novo objeto `Engine` para `engine`
passando o argumento `V8` como o argumento `name` para seu construtor.