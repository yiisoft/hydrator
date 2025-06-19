<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator;

use Yiisoft\Hydrator\AttributeHandling\DataAttributesHandler;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\AttributeResolverFactoryInterface;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ReflectionAttributeResolverFactory;
use Yiisoft\Hydrator\Internal\InternalHydrator;
use Yiisoft\Hydrator\Internal\ObjectFactoryWithoutConstructor;
use Yiisoft\Hydrator\TypeCaster\CompositeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;

final class NoConstructorHydrator implements HydratorInterface
{
    private InternalHydrator $internalHydrator;

    /**
     * @param TypeCasterInterface|null $typeCaster Type caster used to cast raw values.
     */
    public function __construct(
        ?TypeCasterInterface $typeCaster = null,
        ?AttributeResolverFactoryInterface $attributeResolverFactory = null,
    ) {
        $typeCaster ??= new CompositeTypeCaster(
            new PhpNativeTypeCaster(),
            new HydratorTypeCaster(),
        );
        $attributeResolverFactory ??= new ReflectionAttributeResolverFactory();
        $this->internalHydrator = new InternalHydrator(
            $typeCaster,
            new DataAttributesHandler($attributeResolverFactory),
            new ParameterAttributesHandler($attributeResolverFactory, $this),
            new ObjectFactoryWithoutConstructor(),
            $this,
        );
    }

    public function hydrate(object $object, array|DataInterface $data = []): void
    {
        $this->internalHydrator->hydrate($object, $data);
    }

    public function create(string $class, array|DataInterface $data = []): object
    {
        return $this->internalHydrator->create($class, $data);
    }
}
