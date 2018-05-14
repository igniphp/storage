<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;
use Igni\Utils\ReflectionApi\RuntimeMethod;

final class DecimalNumber implements MappingStrategy, DefaultAttributesProvider
{
    public static function hydrate(&$value, array $attributes = []): void
    {
        $value = self::formatDecimalNumber((string) $value, $attributes);
    }

    public static function extract(&$value, array $attributes = []): void
    {
        $value = self::formatDecimalNumber((string) $value, $attributes);
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'scale' => 10,
            'precision' => 2,
        ];
    }

    private static function formatDecimalNumber(string $number, array $attributes): string
    {
        $parts = explode('.', $number);
        if (strlen($parts[0]) > $attributes['scale']) {
            $parts[0] = str_repeat('9', $attributes['scale']);
        }
        $number = $parts[0] . '.' . ($parts[1] ?? '');

        return bcadd($number, '0', $attributes['precision']);
    }
}
