# Configuration with PHP attributes

You can configure how the hydrator creates or hydrates a specific class using attributes.

## Skipping hydration

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
ues \Yiisoft\Hydrator\Attribute\Parameter\Di;

class MyClass
{
    public function __construct(
        #[Di(id: 'importConnection')]
        private ConnectionInterface $connection,
    ) {}
}
```

The annotation will instruct hydrator to get `$connection` from DI container by `importConnection` ID.
