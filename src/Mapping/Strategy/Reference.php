<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class Reference implements MappingStrategy
{
    public static function getHydrator(): string
    {
        return '
        $value = $this->entityManager->get($attributes[\'class\'], $value);';
    }

    public static function getExtractor(): string
    {
        return '
        if ($value instanceof Entity) {
            $value = $value->getId() ? $value->getId()->getValue() : null;
        } else {
            $value = null;
        }';
    }
}
