<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class FloatNumber implements MappingStrategy
{
    public static function hydrate(&$value): void
    {
        $value = (float) $value;;
    }

    public static function extract(&$value): void
    {
        $value = (float) $value;
    }
}
