<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class Integer implements MappingStrategy
{
    public static function getHydrator(): string
    {
        return '
        $value = (int) $value;';
    }

    public static function getExtractor(): string
    {
        return '
        $value = (int) $value;';
    }
}
