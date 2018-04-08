<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;

final class Text implements MappingStrategy
{
    public static function hydrate($value, MappingContext $context, array $options = []): string
    {
        return self::trimString($value, $options);
    }

    public static function extract($value, MappingContext $context, array $options = []): string
    {
        return self::trimString($value, $options);
    }

    private static function trimString($value, array $options = []): string
    {
        if (isset($options['length'])) {
            return substr((string) $value, 0, (int) $options['length']);
        }

        return $value;
    }
}
