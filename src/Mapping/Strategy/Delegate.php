<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class Delegate implements MappingStrategy
{
    public static function getHydrator(): string
    {
        return '';
    }

    public static function getExtractor(): string
    {
        return '';
    }
}
