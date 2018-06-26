<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\MappingStrategy;

final class Enum implements MappingStrategy, DefaultAttributesProvider
{
    public static function hydrate(&$value, array $attributes = []): void
    {
        if ($value === null) {
            return;
        }

        $values = $attributes['values'];

        if (is_string($values) && class_exists($values)) {
            $value = new $values($value);
            return;
        }

        if (is_array($values) && !empty($values)) {
            $value = $values[$value];
            return;
        }

        throw MappingException::forInvalidAttributeValue('values', $values, 'Is not valid class name or available value list.');
    }

    public static function extract(&$value, array $attributes = []): void
    {
        if ($value instanceof \Igni\Storage\Enum) {
            $value = $value->getValue();
            return;
        }

        $values = $attributes['values'];

        if (!is_array($values)) {
            throw MappingException::forInvalidAttributeValue('values', $values, 'Is not valid class name or available value list.');
        }

        $value = array_search($value, $attributes['values']);
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'values' => [],
        ];
    }
}
