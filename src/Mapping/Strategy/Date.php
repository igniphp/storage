<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Igni\Storage\Mapping\MappingStrategy;

final class Date implements MappingStrategy, DefaultAttributesProvider
{
    public static function hydrate(&$value, array $attributes = []): void
    {
        if ($value === null) {
            return;
        }

        if ($attributes['immutable']) {
            $value = new DateTimeImmutable($value, new DateTimeZone($attributes['timezone']));
            return;
        }

        $value = new DateTime($value, new DateTimeZone($attributes['timezone']));
    }

    public static function extract(&$value, array $attributes = []): void
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format($attributes['format']);
        } else {
            $value = null;
        }
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'timezone' => 'UTC',
            'format' => 'Ymd',
            'immutable' => false,
        ];
    }
}
