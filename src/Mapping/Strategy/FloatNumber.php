<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;

final class FloatNumber implements MappingStrategy
{
    public static function hydrate($value, MappingContext $context, array $options = [])
    {
        return (float) $value;
    }

    public static function extract($value, MappingContext $context, array $options = [])
    {
        return (float) $value;
    }
}
