<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class Enum implements MappingStrategy, DefaultAttributesProvider
{
    public static function hydrate(&$value, array $attributes = []): void
    {
        $value = $attributes['values'][$value];
    }

    public static function extract(&$value, array $attributes = []): void
    {
        $value = array_search($value, $attributes['values']);
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'values' => [],
        ];
    }
}
