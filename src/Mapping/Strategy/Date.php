<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;
use DateTime;
use DateTimeZone;

final class Date implements MappingStrategy, DefaultOptionsProvider
{
    /**
     * @param $value
     * @param MappingContext $context
     * @param array $options
     * @return DateTime|null
     */
    public static function hydrate($value, MappingContext $context, array $options = []): ?DateTime
    {
        if ($value === null) {
            return null;
        }

        return new DateTime($value, new DateTimeZone($options['timezone']));
    }

    /**
     * @param \DateTimeInterface $value
     * @param MappingContext $context
     * @param array $options
     * @return null|string
     */
    public static function extract($value, MappingContext $context, array $options = []): ?string
    {
        if ($value === null) {
            return $value;
        }

        return $value->format($options['format']);
    }

    public static function getDefaultOptions(): array
    {
        return [
            'timezone' => new DateTimeZone('UTC'),
            'format' => 'Ymd',
        ];
    }
}
