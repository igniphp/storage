<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class DecimalNumber implements MappingStrategy, DefaultAttributesProvider
{
    public static function getHydrator(): string
    {
        return '
        $value = \Igni\Storage\Mapping\Strategy\DecimalNumber::formatDecimalNumber((string) $value, $attributes);
        ';
    }

    public static function getExtractor(): string
    {
        return '
        $value = \Igni\Storage\Mapping\Strategy\DecimalNumber::formatDecimalNumber((string) $value, $attributes);
        ';
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'scale' => 10,
            'precision' => 2,
        ];
    }

    public static function formatDecimalNumber(string $number, array $attributes): string
    {
        $parts = explode('.', $number);
        if (strlen($parts[0]) > $attributes['scale']) {
            $parts[0] = str_repeat('9', $attributes['scale']);
        }
        $number = $parts[0] . '.' . ($parts[1] ?? '');

        return bcadd($number, '0', $attributes['precision']);
    }
}
