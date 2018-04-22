<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class Text implements MappingStrategy
{
    public static function getHydrator(): string
    {
        return '
        $value = \Igni\Storage\Mapping\Strategy\Text::trimString($value, $attributes);';
    }

    public static function getExtractor(): string
    {
        return '
        $value = \Igni\Storage\Mapping\Strategy\Text::trimString($value, $attributes);';
    }

    public static function trimString($value, array $attributes = []): string
    {
        if (isset($attributes['length'])) {
            return substr((string) $value, 0, (int) $attributes['length']);
        }

        return (string) $value;
    }
}
