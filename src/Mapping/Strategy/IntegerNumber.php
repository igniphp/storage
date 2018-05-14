<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class IntegerNumber implements MappingStrategy
{
    public static function hydrate(&$value): void
    {
        $value = (int) $value;
    }

    public static function extract(&$value): void
    {
        $value = (int) $value;
    }
}
