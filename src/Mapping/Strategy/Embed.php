<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Exception\HydratorException;
use Igni\Storage\Mapping\MappingStrategy;

final class Embed implements MappingStrategy, DefaultAttributesProvider
{
    public static function getHydrator(): string
    {
        return '
        if (!empty($value)) {
            $value = \Igni\Storage\Mapping\Strategy\Embed::deserializeValue($value, $attributes[\'storeAs\']);
            if (!empty($value)) {
                $value = $entityManager->hydrate($attributes[\'class\'], $value);
            } else {
                $value = null;
            }
        } else {
            $value = null;
        }';
    }

    public static function getExtractor(): string
    {
        return '
        if ($value instanceof $attributes[\'class\']) {
            $value = $entityManager->extract($value);
            $value = \Igni\Storage\Mapping\Strategy\Embed::serializeValue($value, $attributes[\'storeAs\']);
        } else {
            $value = null;
        }';
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'storeAs' => 'json',
        ];
    }

    public static function deserializeValue($value, string $strategy)
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

    public static function serializeValue($value, string $strategy)
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
