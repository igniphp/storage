<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

interface MappingStrategy
{
    public static function hydrate(&$value);
    public static function extract(&$value);
}
