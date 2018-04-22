<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class Embed implements MappingStrategy, DefaultAttributesProvider
{
    public static function getHydrator(): string
    {
        return '
        switch ($attributes[\'storeAs\']) {
            case \'json\':
                $value = json_decode($value, true);
                break;
            case \'serialized\':
                $value = unserialize($value);
                break;
            case \'plain\':
                break;
            default:
                throw new HydratorException("Cannot hydrate embed entity, invalid store attribute ({$attributes[\'store_as\']})");
        }

        $value = $this->entityManager->hydrate($attributes[\'class\'], $value);';
    }

    public static function getExtractor(): string
    {
        return '
        $value = $this->entityManager->extract($value);
        switch ($attributes[\'storeAs\']) {
            case \'json\':
                $value = json_encode($value);
                break;
            case \'serialized\':
                $value = serialize($value);
                break;
            case \'plain\':
                break;
            default:
                throw new HydratorException("Cannot extract embed entity, invalid store option ({$attributes[\'store_as\']})");
        }';
    }

    public static function getDefaultAttributes(): array
    {
        return [
            'store_as' => 'json',
        ];
    }
}
