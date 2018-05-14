<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Entity;
use Igni\Storage\EntityManager;
use Igni\Storage\Mapping\MappingStrategy;

final class Reference implements MappingStrategy
{
    public static function hydrate(&$value, array $attributes = [], EntityManager $manager = null): void
    {
        if ($value) {
            try {
                $value = $manager->get($attributes['target'], $value);
            } catch (\Exception $e) {
                $value = null;
            }
        }
    }

    public static function extract(&$value, array $attributes = [], EntityManager $manager = null): void
    {
        if ($value instanceof Entity) {
            $value = $value->getId() ? $value->getId()->getValue() : null;
        } else {
            $value = null;
        }
    }
}
