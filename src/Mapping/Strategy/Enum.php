<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;

final class Enum implements MappingStrategy, DefaultAttributesProvider
{
    public static function getHydrator(): string
    {
        return '
        $value = $attributes[\'values\'][$value];';
    }

    public static function getExtractor(): string
    {
        return '
        $value = array_search($value, $attributes[\'values\']);';
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'values' => [],
        ];
    }
}
