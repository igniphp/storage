<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;
use Igni\Storage\Uuid;

final class Id implements MappingStrategy, DefaultOptionsProvider
{
    public static function hydrate($value, MappingContext $context, array $options = [])
    {
        $class = $options['generator'];

        return new $class($value);
    }

    public static function extract($value, MappingContext $context, array $options = [])
    {
        return $value->getValue();
    }

    public static function getDefaultOptions(): array
    {
        return [
            'generator' => Uuid::class,
        ];
    }
}
