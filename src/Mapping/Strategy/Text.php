<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class Text implements MappingStrategy
{
    public static function hydrate(&$value, array $attributes = []): void
    {
        $value = self::trimString($value, $attributes);
    }

    public static function extract(&$value, array $attributes = []): void
    {
        $value = self::trimString($value, $attributes);
    }

    private static function trimString($value, array $attributes = []): string
    {
        if (isset($attributes['length'])) {
            return substr((string) $value, 0, (int) $attributes['length']);
        }

        return (string) $value;
    }
}
