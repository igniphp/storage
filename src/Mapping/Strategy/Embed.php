<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Exception\MappingException;
use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;

final class Embed implements MappingStrategy, DefaultOptionsProvider
{
    public static function hydrate($value, MappingContext $context, array $options = [])
    {
        switch ($options['store_as']) {
            case 'json':
                $value = json_decode($value, true);
                break;
            case 'serialized':
                $value = unserialize($value);
                break;
            case 'plain':
                break;
            default:
                throw MappingException::forUnknownMappingStrategy(Embed::class . "(store_as {$options['store_as']})");
        }

        return $context->getEntityHydrator()->hydrate($value);
    }

    public static function extract($value, MappingContext $context, array $options = [])
    {
        $value = $context->getEntityHydrator()->extract($value);
        switch ($options['store_as']) {
            case 'json':
                $value = json_encode($value);
                break;
            case 'serialized':
                $value = serialize($value);
                break;
            case 'plain':
                break;
            default:
                throw MappingException::forUnknownMappingStrategy(Embed::class . "(store_as {$options['store_as']})");
        }
        return $value;
    }

    public static function getDefaultOptions(): array
    {
        return [
            'store_as' => 'json',
        ];
    }
}
