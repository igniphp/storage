<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Storable;
use Igni\Storage\Manager;
use Igni\Storage\Mapping\MappingStrategy;

final class Reference implements MappingStrategy
{
    public static function hydrate(&$value, array $attributes = [], Manager $manager = null): void
    {
        if ($value) {
            try {
                $value = $manager->get($attributes['target'], $value);
            } catch (\Exception $e) {
                $value = null;
            }
        }
    }

    public static function extract(&$value, array $attributes = [], Manager $manager = null): void
    {
        if ($value instanceof Storable) {
            $value = $value->getId() ? $value->getId()->getValue() : null;
        } else {
            $value = null;
        }
    }
}
