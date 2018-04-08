<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

interface MappingStrategy
{
    public static function hydrate($value, MappingContext $context, array $options = []);
    public static function extract($value, MappingContext $context, array $options = []);
}
