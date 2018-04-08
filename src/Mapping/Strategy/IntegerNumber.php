<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;

final class IntegerNumber implements MappingStrategy
{
    public static function hydrate($value, MappingContext $context, array $options = [])
    {
        return (int) $value;
    }

    public static function extract($value, MappingContext $context, array $options = [])
    {
        return (int) $value;
    }
}
