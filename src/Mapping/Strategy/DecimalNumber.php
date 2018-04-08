<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;

final class DecimalNumber implements MappingStrategy, DefaultOptionsProvider
{
    public static function hydrate($value, MappingContext $context, array $options = []): string
    {
        return (string) $value;
    }

    public static function extract($value, MappingContext $context, array $options = []): string
    {
        return (string) $value;
    }

    public static function getDefaultOptions(): array
    {
        return [
            'scale' => 10,
            'precision' => 2,
        ];
    }
}
