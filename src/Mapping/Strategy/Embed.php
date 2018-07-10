<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\EntityManager;
use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Mapping\MappingStrategy;

final class Embed implements MappingStrategy, DefaultAttributesProvider
{
    public static function hydrate(&$value, array $attributes = [], EntityManager $manager = null): void
    {
        if (!empty($value)) {
            $value = self::deserializeValue($value, $attributes['storeAs']);
            if (!empty($value)) {
                $value = $manager->hydrate($attributes['class'], $value);
            } else {
                $value = null;
            }
        } else {
            $value = null;
        }
    }

    public static function extract(&$value, array $attributes = [], EntityManager $manager = null): void
    {

        if ($value instanceof $attributes['class']) {
            $value = $manager->extract($value);
            $value = self::serializeValue($value, $attributes['storeAs']);
        } else {
            $value = null;
        }
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'storeAs' => 'json',
        ];
    }

    private static function deserializeValue($value, string $strategy)
    {
        switch ($strategy) {
            case 'json':
                $value = json_decode($value, true);
                break;
            case 'serialized':
                $value = unserialize($value);
                break;
            case 'plain':
                break;
            default:
                throw new HydratorException("Cannot persist embed entity, invalid storeAs attribute (${strategy})");
        }

        return $value;
    }

    private static function serializeValue($value, string $strategy)
    {
        switch ($strategy) {
            case 'json':
                $value = json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
                break;
            case 'serialized':
                $value = serialize($value);
                break;
            case 'plain':
                break;
            default:
                throw new HydratorException("Cannot hydrate embed entity, invalid storeAs attribute (${strategy})");
        }

        return $value;
    }
}
