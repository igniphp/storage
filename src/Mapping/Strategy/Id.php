<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\GenericId;
use Igni\Storage\Mapping\MappingStrategy;

final class Id implements MappingStrategy, DefaultAttributesProvider
{
    public static function hydrate(&$value, $attributes = []): void
    {
        $class = $attributes['class'];
        $value = new $class($value);
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
            'class' => GenericId::class,
        ];
    }
}
