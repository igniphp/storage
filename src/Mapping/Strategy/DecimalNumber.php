<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\MappingStrategy;

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
            'scale' => 2,
            'precision' => 10,
        ];
    }

    private static function formatDecimalNumber(string $number, array $attributes): string
    {
        if ($attributes['scale'] > $attributes['precision']) {
            throw MappingException::forInvalidAttributeValue(
                'scale',
                $attributes['scale'],
                'Attribute `scale` must be lower than `precision`.'
            );
        }

        $parts = explode('.', $number);
        $decimals = $attributes['precision'] - $attributes['scale'];

        if (strlen($parts[0]) > $decimals) {
            $parts[0] = str_repeat('9', $decimals);
        }

        $number = $parts[0] . '.' . ($parts[1] ?? '');

        return bcadd($number, '0', $attributes['scale']);
    }
}
