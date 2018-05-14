<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Mapping\Uuid;

final class Id implements MappingStrategy, DefaultAttributesProvider
{
    public static function hydrate(&$value, $attributes = []): void
    {
        $generator = $attributes['generator'];
        $value = new $generator($value);
    }

    public static function extract(&$value, $attributes = []): void
    {
        if ($value instanceof \Igni\Storage\Id) {
            $value = $value->getValue();
        } else {
            $value = (string) $value;
        }
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'generator' => Uuid::class,
        ];
    }
}
