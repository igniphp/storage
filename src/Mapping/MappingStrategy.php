<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

interface MappingStrategy
{
    public static function getHydrator(): string;
    public static function getExtractor(): string;
}
