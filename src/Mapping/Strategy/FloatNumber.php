<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class FloatNumber implements MappingStrategy
{
    public function hydrate($value): float
    {
        return (float) $value;
    }

    public function extract($value): float
    {
        return (float) $value;
    }
}
