<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class Date implements MappingStrategy, DefaultAttributesProvider
{
    public static function getHydrator(): string
    {
        return '
        if ($value !== null) {
            $value = new \DateTime($value, new \DateTimeZone($attributes[\'timezone\']));
        }';
    }

    public static function getExtractor(): string
    {
        return '
        if ($value !== null) {
            $value = $value->format($attributes[\'format\']);
        }';
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'timezone' => 'UTC',
            'format' => 'Ymd',
        ];
    }
}
