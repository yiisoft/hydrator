<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use Attribute;
use Yiisoft\Hydrator\ParameterAttributeInterface;

/**
 * Maps a property or parameter to an instance obtained from container by the specified id.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Di implements ParameterAttributeInterface
{
    /**
     * @param string $id Container id to obtain instance from.
     */
    public function __construct(
        private string $id
    ) {
    }

    /**
     * Get container id to obtain instance from.
     *
     * @return string Container id to obtain instance from.
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function getResolver(): string
    {
        return DiResolver::class;
    }
}
