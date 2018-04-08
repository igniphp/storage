<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;

final class Enum implements MappingStrategy, DefaultOptionsProvider
{
    public static function hydrate($index, MappingContext $context, array $options = [])
    {
        return $options['values'][$index];
    }

    public static function extract($value, MappingContext $context, array $options = [])
    {
        return array_search($value, $options['values']);
    }

    public static function getDefaultOptions(): array
    {
        return [
            'values' => [],
        ];
    }
}
